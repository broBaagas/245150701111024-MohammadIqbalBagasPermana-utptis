<?php

namespace App\Swagger;

use OpenApi\Attributes as OA;

#[OA\Info(
    title: "Ecommerce Toko Elektronik API",
    version: "1.0.0",
    description: "Dokumentasi API Laravel"
)]
#[OA\Server(
    url: "http://127.0.0.1:8000",
    description: "Local Server"
)]
class OpenApi {}