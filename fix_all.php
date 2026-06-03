<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();
$reqs = App\Models\StudentRequest::where('photo_validation_status', 'pending')->get();
foreach($reqs as $req) {
    $v = new App\Services\PhotoValidationService();
    $res = $v->validate($req->photo_path);
    $req->photo_validation_status = $res['status'];
    $req->photo_validation_details = json_encode($res['details']);
    $req->save();
    echo "Fixed req " . $req->id . "\n";
}
echo "DONE\n";
