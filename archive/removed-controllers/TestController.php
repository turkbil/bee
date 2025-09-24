<?php

namespace App\Http\Controllers;

class TestController extends Controller {
    public function test()
    {
        $result = [
            'cdn_exists' => function_exists('cdn'),
            'tenant_id_exists' => function_exists('tenant_id'),
            'is_tenant_exists' => function_exists('is_tenant'),
            'is_central_exists' => function_exists('is_central'),
            'tenant_name_exists' => function_exists('tenant_name'),
            'tenant_domain_exists' => function_exists('tenant_domain'),
            'tenant_disk_exists' => function_exists('tenant_disk'),
            'tenant_storage_path_exists' => function_exists('tenant_storage_path'),
            'tenant_storage_url_exists' => function_exists('tenant_storage_url')
        ];
        
        return view('test', ['results' => $result]);
    }
}