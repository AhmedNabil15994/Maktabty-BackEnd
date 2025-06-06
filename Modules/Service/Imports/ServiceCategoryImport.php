<?php


namespace Modules\Service\Imports;

use Illuminate\Http\Request;
use Maatwebsite\Excel\Concerns\ToModel;
use Modules\Service\Repositories\Dashboard\ServiceCategoryRepository;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\WithValidation;
use Illuminate\Validation\Rule;

class ServiceCategoryImport implements ToModel, WithHeadingRow, WithValidation
{
    use Importable;

    private $request;
    private $repo;

    public function __construct(Request $request)
    {
        $this->repo = new ServiceCategoryRepository();
        $this->request = $request;
    }

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            '*.'.str_replace(' ' ,'_',strtolower($this->request['status'])) => 'nullable|in:on,off',
            '*.'.str_replace(' ' ,'_',strtolower($this->request['title_ar'])) => 'required',
        ];
    }

    /**
     * @return array
     */
    public function customValidationMessages()
    {
        return [
            '*.'.str_replace(' ' ,'_',strtolower($this->request['status'])).'.in' => 'status must in on or off',
            '*.'.str_replace(' ' ,'_',strtolower($this->request['title_ar'])).'.required' => __('service::dashboard.service_categories.validation.title.required'),
        ];
    }

    /**
     * @param  array  $row
     * @return \Illuminate\Database\Eloquent\Model|\Illuminate\Database\Eloquent\Model[]|null
     * @throws \Exception
     */
    public function model(array $row)
    {
        $request = new Request();

        $request->replace([
            'status' => str_replace(' ' ,'_',strtolower($this->request['status'])) ?
                $row[str_replace(' ' ,'_',strtolower($this->request['status']))] : null,
            'title' => [
                'en' => str_replace(' ' ,'_',strtolower($this->request['title_en'])) ?
                    $row[str_replace(' ' ,'_',strtolower($this->request['title_en']))] : '',
                'ar' => str_replace(' ' ,'_',strtolower($this->request['title_ar'])) ?
                    $row[str_replace(' ' ,'_',strtolower($this->request['title_ar']))]: '',
            ],
        ]);

        return $this->repo->create($request);
    }
}