<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class SetPublicBucketPolicy extends Command
{
    protected $signature = 'app:set-public';

    protected $description = 'Set bucket policy to allow public read for extonan/public/*';

    public function handle(): int
    {
        $s3 = Storage::disk('public')->getClient();

        $bucket = config('filesystems.disks.public.bucket');

        $s3->putBucketPolicy([
            'Bucket' => $bucket,
            'Policy' => json_encode([
                'Version' => '2012-10-17',
                'Statement' => [[
                    'Effect' => 'Allow',
                    'Principal' => '*',
                    'Action' => 's3:GetObject',
                    'Resource' => "arn:aws:s3:::{$bucket}/public/*",
                ]],
            ]),
        ]);

        $this->info("Public read access granted for {$bucket}/public/*");

        return self::SUCCESS;
    }
}
