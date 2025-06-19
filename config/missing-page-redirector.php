<?php

return [
    /*
     * This is the class responsible for providing the urls which must be redirected.
     * The only requirement for the redirector is that it needs to implement the
     * `Spatie\MissingPageRedirector\Redirector\Redirector` interface
     */
    'redirector' => \App\Services\TenantAwareRedirector::class,

    /*
     * By default the package will only redirect 404s. If you want to redirect on other
     * response codes, just add them to the array. Leave the array empty to redirect
     * always.
     */
    'redirect_status_codes' => [
        \Symfony\Component\HttpFoundation\Response::HTTP_NOT_FOUND
    ],

    /*
     * When using the `ConfigurationRedirector` you can specify the redirects in this array.
     * You can use Laravel's route parameters here.
     */
    'redirects' => [
        // 404 sayfalarını tenant anasayfasına yönlendir
        // Bu kısım dinamik olarak çalışacak, config boş bırakıyoruz
    ],

];