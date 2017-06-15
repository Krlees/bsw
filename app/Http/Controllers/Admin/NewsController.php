<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Admin\BaseController;

class NewsController extends BaseController
{
    public function index()
    {
        $tables = [
            'addUrl' => url('admin/news/add'),
            'actionUrl' => url('admin/news/index')
        ];

        return view('admin/News/index', compact('tables'));
    }

    public function add()
    {
        $form = [
            'actionUrl' => '',
            'formTitle' => '添加',
        ];
        $formFields = [];

        return view('admin/News/add', compact('form', 'formFields'));
    }

}
