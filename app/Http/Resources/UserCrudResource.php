<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserCrudResource extends JsonResource
{
    public static $wrap = false;
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "id" => $this->id,
            "name" => $this->name,
            "email" => $this->email,
            //'created_at' => (new \Carbon\Carbon($this->created_at))->format('Y-m-d'),
            'created_at' => (new \Carbon\Carbon($this->created_at))->format('Y-m-d H:i:s'),
        ];
    }
}
