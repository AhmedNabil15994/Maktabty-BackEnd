<template>

     
    <div class="product-blk" data-toggle="tooltip" data-placement="bottom" title="Product Code">
        <span class="pro-price" v-if="product.price > 0" >
            <product-price :product="product" />
        </span>
        <!-- <span class="pro-price"  v-else>
            
        </span> -->
        <div class="img-box">
            <img class="img-fluid" :src="product.image" alt="" />
        </div>
        <h3 class="pro-name">{{product.title}}</h3>
        <p class="pro-category">{{product.sku}}</p>
    </div>
 

</template>

<script>
    import ProductPrice from './ProductPriceComponent'
    export default {
       props:["product"] ,
       components:{
           ProductPrice
       }
    }
</script>
