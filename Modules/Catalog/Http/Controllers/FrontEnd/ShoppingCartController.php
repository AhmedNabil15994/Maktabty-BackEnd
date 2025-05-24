<?php

namespace Modules\Catalog\Http\Controllers\FrontEnd;

use Cart;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\File;
use Modules\Attribute\Entities\Attribute;
use Modules\Attribute\Enums\AttributeType;
use Modules\Catalog\Http\Requests\FrontEnd\CartRequest;
use Modules\Catalog\Repositories\FrontEnd\ProductRepository as Product;
use Modules\Catalog\Traits\ShoppingCartTrait;
use Modules\Core\Traits\CoreTrait;

class ShoppingCartController extends Controller
{
    use ShoppingCartTrait, CoreTrait;

    protected $product;
    protected $attribute;

    public function __construct(Product $product, Attribute $attribute)
    {
        $this->product = $product;
        $this->attribute = $attribute;
    }

    public function index()
    {
        $items = getCartContent();
        return view('catalog::frontend.shopping-cart.index', compact('items'/* , 'cartGifts', 'cartCards', 'addonsCards' */));
    }

    public function totalCart()
    {
        return priceWithCurrenciesCode(getCartSubTotal());
    }

    public function headerCart()
    {
        return view('apps::frontend.layouts._cart');
    }

    public function createOrUpdate(CartRequest $request, $productSlug, $variantPrdId = null)
    {
        $userToken = $this->getCartUserToken();
        $data = [];
        if (isset($request->product_type) && $request->product_type == 'variation') {
            $product = $this->product->findVariantProductById($variantPrdId);
            $product->product_type = 'variation';
            $routeParams = [$product->product->slug, generateVariantProductData($product->product, $variantPrdId, json_decode($request->selectedOptionsValue))['slug']];
            $data['productDetailsRoute'] = route('frontend.products.index', $routeParams);
            $data['productTitle'] = generateVariantProductData($product->product, $variantPrdId, json_decode($request->selectedOptionsValue))['name'];
            $productCartId = 'var-' . $product->id;
        } else {
            $product = $this->product->findBySlug($productSlug);
            $product->product_type = 'product';
            $data['productDetailsRoute'] = route('frontend.products.index', [$product->slug]);
            $data['productTitle'] = $product->title;
            $productCartId = $product->id;

            if (count($product->variants) > 0) {
                return response()->json(["errors" => __('catalog::frontend.cart.product_have_variations_it_cannot_be_ordered')], 422);
            }
        }

        if (!$product) {
            abort(404);
        }

        $checkProduct = is_null(getCartItemById($productCartId));

        if (isset($request->request_type) && $request->request_type == 'general_cart') {
            $request->merge(['qty' => getCartItemById($product->id) ? getCartItemById($product->id)->quantity + 1 : 1]);
        }

        if (isset($request->request_type) && $request->request_type == 'cart') {
            // remove cart gifts
            $oldQuantity = getCartItemById($product->id)->quantity;
            $newQuantity = $request->qty;
            if (intval($newQuantity) > intval($oldQuantity)) {
                $removeCartGifts = true;
                Cart::session($userToken)->removeCartCondition('gift');
            }
        }

        $productAttributes = [];
        /* Start - Upload product input attributes files */
        if (isset($request->productAttributes) && !is_null($request->productAttributes)) {
            $productAttributes = $this->uploadProductInputAttributesFiles($productCartId, $request->productAttributes);
            if (gettype($productAttributes) == 'string') {
                return response()->json(["errors" => $productAttributes], 422);
            }
        }
        $request->request->add(['newProductAttributes' => $productAttributes]);
        /* End - Upload product input attributes files */

        $errors = $this->addOrUpdateCart($product, $request);

        if ($errors) {
            return response()->json(["errors" => $errors], 422);
        }

        // add product attributes prices to cart product in condition over total
        $this->addProductAttributesPricesCondition($product->id, $productCartId, $request->productAttributes ?? []);

        $data["total"] = priceWithCurrenciesCode(getCartTotal());
        $data["subTotal"] = priceWithCurrenciesCode(getCartSubTotal());
        $data["cartCount"] = count(getCartContent(null, true));
        //        $data["productPrice"] = $product->offer ? $product->offer->offer_price : $product->price;

        if ($product->offer) {
            if (!is_null($product->offer->offer_price)) {
                $data["productPrice"] = priceWithCurrenciesCode($product->offer->offer_price);
            } elseif (!is_null($product->offer->percentage)) {
                $percentageResult = (floatval($product->price) * floatVal($product->offer->percentage)) / 100;
                $data["productPrice"] = priceWithCurrenciesCode(floatval($product->price) - $percentageResult);
            } else {
                $data["productPrice"] = priceWithCurrenciesCode(floatval($product->price));
            }
        } else {
            $data["productPrice"] = priceWithCurrenciesCode(floatval($product->price));
        }

        $data["productQuantity"] = $request->product_type == 'product' ? getCartItemById($productCartId)->quantity : getCartItemById($productCartId)->quantity;
        $data["product_type"] = $request->product_type ?? '';
        $data["remainingQty"] = intval($product->qty) - intval($data["productQuantity"]);
        $data["removeCartGifts"] = $removeCartGifts ?? false;

        if ($checkProduct) {
            return response()->json(["message" => __('catalog::frontend.cart.add_successfully'), "data" => $data], 200);
        } else {
            return response()->json(["message" => __('catalog::frontend.cart.updated_successfully'), "data" => $data], 200);
        }
    }

    public function delete(Request $request, $id)
    {
        $productCartId = $request->product_type == 'product' ? $id : 'var-' . $id;
        $this->deleteOldCartProductAttributesFiles($productCartId);
        $deleted = $this->deleteProductFromCart($productCartId);

        if ($deleted) {
            return redirect()->back()->with(['alert' => 'success', 'status' => __('catalog::frontend.cart.delete_item')]);
        }

        return redirect()->back()->with(['alert' => 'danger', 'status' => __('catalog::frontend.cart.error_in_cart')]);
    }

    public function deleteByAjax(Request $request)
    {
        $productCartId = $request->product_type == 'product' ? $request->id : 'var-' . $request->id;
        $this->deleteOldCartProductAttributesFiles($productCartId);
        $deleted = $this->deleteProductFromCart($productCartId);

        if ($deleted) {
            $result["cartCount"] = count(getCartContent(null, true));
            $result["cartTotal"] = priceWithCurrenciesCode(getCartSubTotal());
            return response()->json(["message" => __('catalog::frontend.cart.delete_item'), "result" => $result], 200);
        }

        return response()->json(["errors" => __('catalog::frontend.cart.error_in_cart')], 422);
    }

    public function clear(Request $request)
    {
        $cleared = $this->clearCart();

        if ($cleared) {
            return redirect()->back()->with(['alert' => 'success', 'status' => __('catalog::frontend.cart.clear_cart')]);
        }

        return redirect()->back()->with(['alert' => 'danger', 'status' => __('catalog::frontend.cart.error_in_cart')]);
    }

    public function uploadProductInputAttributesFiles($productCartId, $productAttributes)
    {
        $checkProduct = getCartItemById($productCartId);
        $oldProductAttributes = !is_null($checkProduct) ? ($checkProduct->attributes['productAttributes'] ?? []) : [];
        $oldProductAttributesDiff = array_diff_key($oldProductAttributes, $productAttributes);

        foreach ($productAttributes as $key => $prdAttribute) {
            $attributeObject = $this->attribute->active()->find($key);
            if (!is_null($attributeObject)) {

                // check attribute validation
                if (isset($attributeObject->validation['required']) && $attributeObject->validation['required'] == 1 && empty($prdAttribute)) {
                    return __('catalog::frontend.cart.validations.attributes.required') . ' : ' . $attributeObject->name;
                }

                if (isset($attributeObject->validation['validate_max']) && $attributeObject->validation['validate_max'] == 1 && strlen($prdAttribute) > $attributeObject->validation['max']) {
                    return __('catalog::frontend.cart.validations.attributes.max') . ' : ' . $attributeObject->name . ' ' . __('catalog::frontend.cart.validations.attributes.is') . ' ' . $attributeObject->validation['max'];
                }

                if (isset($attributeObject->validation['validate_min']) && $attributeObject->validation['validate_min'] == 1 && strlen($prdAttribute) < $attributeObject->validation['min']) {
                    return __('catalog::frontend.cart.validations.attributes.min') . ' : ' . $attributeObject->name . ' ' . __('catalog::frontend.cart.validations.attributes.is') . ' ' . $attributeObject->validation['min'];
                }

                if (!is_null($attributeObject) && $prdAttribute instanceof UploadedFile) { // check if a value is an image
                    if (!empty($oldProductAttributes) && key_exists($key, $oldProductAttributes)) {
                        File::delete($oldProductAttributes[$key]);
                    }
                    ### Delete old image

                    $imgName = $this->uploadImage(public_path(config('core.config.cart_img_path')), $prdAttribute);
                    $productAttributes[$key] = config('core.config.cart_img_path') . '/' . $imgName;
                }

                if (in_array($attributeObject->type, AttributeType::$allowOptions)) {
                    $productAttributes['prices'][$key] = $attributeObject->options->find($prdAttribute)->price ?? null;
                } else {
                    $productAttributes['prices'][$key] = $attributeObject->price;
                }
            }
        }

        foreach ($oldProductAttributesDiff as $key => $prdAttribute) {
            $attributeObject = $this->attribute->active()->find($key);
            if (!is_null($attributeObject)) {
                if (!is_null($oldProductAttributes[$key]) && $attributeObject->type == 'file') {
                    $productAttributes[$key] = $oldProductAttributes[$key];
                }

                if (in_array($attributeObject->type, AttributeType::$allowOptions)) {
                    $productAttributes['prices'][$key] = $attributeObject->options->find($prdAttribute)->price ?? null;
                } else {
                    $productAttributes['prices'][$key] = $attributeObject->price;
                }
            }
        }
        return $productAttributes;
    }

    protected function deleteOldCartProductAttributesFiles($productCartId)
    {
        $checkProduct = getCartItemById($productCartId);
        $oldProductAttributes = !is_null($checkProduct) ? ($checkProduct->attributes['productAttributes'] ?? []) : [];
        if (count($oldProductAttributes) > 0) {
            foreach ($oldProductAttributes as $key => $oldProductAttribute) {
                $attributeObject = $this->attribute->find($key);
                if (!is_null($attributeObject) && $attributeObject->type == 'file') {
                    File::delete($oldProductAttribute); ### Delete old image
                }
            }
        }
    }

    private function addProductAttributesPricesCondition($productId, $productCartId, $productAttributes)
    {
        $userToken = $this->getCartUserToken();
        if (!empty($productAttributes)) {
            $conditions = [];
            foreach (array_keys($productAttributes) as $key => $id) {

                $activeAttribute = $this->attribute->active()->find($id);

                if ($activeAttribute) {

                    if (in_array($activeAttribute->type, AttributeType::$allowOptions)) {
                        $optionId = $productAttributes[$id] ?? null;
                    } else {
                        $optionId = null;
                    }

                    $attributePrice = null;
                    if (!is_null($activeAttribute->price)) {
                        $attributePrice = $activeAttribute->price;
                    } elseif (!empty($activeAttribute->options)) {
                        $attributePrice = $activeAttribute->options->find($optionId)->price ?? null;
                    }

                    $conditionName = 'cart_' . $productCartId . '_attribute_' . $id;
                    if (!is_null($attributePrice)) {
                        $conditions[] = new \Darryldecode\Cart\CartCondition(array(
                            'name' => $conditionName,
                            'type' => 'product_attribute',
                            'target' => 'subtotal',
                            'value' => $attributePrice,
                            'attributes' => [
                                'product_cart_id' => $productCartId,
                                'product_id' => $productId,
                                'attribute_id' => $id,
                                'option_id' => $optionId,
                            ],
                        ));
                    } else {
                        Cart::session($userToken)->removeCartCondition($conditionName);
                    }
                }
            }
            if (!empty($conditions)) {
                Cart::session($userToken)->condition($conditions);
            }
        }
    }
}
