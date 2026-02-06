<?php

namespace VanOns\FilamentAttachmentLibrary\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use VanOns\FilamentAttachmentLibrary\Facades\FilamentAttachmentLibrary;

class ConfigureAttachmentDirectory
{
    public function handle(Request $request, Closure $next): Response
    {
        FilamentAttachmentLibrary::handleSetDirectory();

        return $next($request);
    }
}
