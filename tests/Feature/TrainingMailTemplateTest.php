<?php

namespace Tests\Feature;

use Tests\TestCase;

class TrainingMailTemplateTest extends TestCase
{
    public function test_training_mail_views_use_shared_template(): void
    {
        foreach (glob(resource_path('views/mail/training/*.blade.php')) ?: [] as $path) {
            $contents = (string) file_get_contents($path);

            $this->assertStringContainsString(
                "@extends('mail.layouts.training')",
                $contents,
                basename($path).' must extend the shared PGTP mail template.'
            );
        }
    }
}
