<?php

namespace Tests\Unit;

use App\Http\Requests\PostStoreRequest;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class PostStoreRequestTest extends TestCase
{
    /**
     * A basic unit test example.
     */
    public function test_validation_fails_without_status(): void
    {
        $request = new PostStoreRequest();
        $validator = Validator::make(['body'=>'x'], $request->rules());
        $this->assertTrue($validator->fails());
    }

}
