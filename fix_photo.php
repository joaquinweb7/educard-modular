<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$req = App\Models\StudentRequest::find(3);
if ($req) {
    $v = new App\Services\PhotoValidationService();
    $res = $v->validate($req->photo_path);
    $req->photo_validation_status = $res['status'];
    $req->photo_validation_details = json_encode($res['details']);
    $req->save();
    echo "DONE\n";
    print_r($res);
} else {
    echo "Request 3 not found\n";
}
