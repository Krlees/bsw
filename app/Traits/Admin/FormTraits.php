<?php
namespace App\Traits\Admin;

trait FormTraits
{
    protected $formField = []; // 表单字段集合

    /**
     * @return array
     */
    public function getFormField()
    {
        return $this->formField;
    }

    /**
     * 返回组装的字段数据【辅助表单】
     *
     * @param $type
     * @param string $title
     * @param $name
     * @param null $value
     * @param $options
     * @param null $tips
     * @return array
     */
    public function createField($type, $title = '', $name, $value = '', $options = [], $tips = null)
    {
        $this->formField[] = compact('type', 'title', 'name', 'value', 'options', 'tips');
        return $this;
    }

    /**
     * 表格数据回调
     *
     * @param $searchUrl
     * @param array $searchField
     * @return array
     */
    public function responseTable($searchUrl = '', $action = [])
    {

        $action['add'] = $action['addUrl'] ?: false;
        $action['remove'] = $action['removeUrl'] ?: false;

        return compact('searchUrl', 'searchField', 'isForm', 'action');
    }

    /**
     * 表单字段统一回调
     *
     * @param $formTitle  表单标题,如:添加产品
     * @param $formField  表单字段数据
     * @param $formUrl    表单提交地址
     */
    public function responseForm($formTitle = '', $formField = [], $formUrl = null)
    {
        return compact('formTitle', 'formField', 'formUrl');
    }

    /**
     * 返回已处理好的【select框】商品分类
     *
     * @param $data     数据库查询的二维数组
     * @param $name
     * @param $value
     * @param $checkid  默认选择id, 为0则不帅选
     * @return array
     */
    public function cleanSelect($data = [], $name='name', $value='id', $checkid = 0)
    {
        if( !is_array($data)){
            return $data;
        }

        $return = [];
        foreach ($data as $k => $v) {

            $return[$k]['text'] = $v[$name];
            $return[$k]['value'] = $v[$value];
            if ($checkid == $v[$value]) {
                $return[$k]['checked'] = true;
            }
        }

        return $return;
    }
}