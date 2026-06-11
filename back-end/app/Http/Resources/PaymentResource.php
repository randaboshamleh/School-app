<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PaymentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'student_id' => $this->student_id,
            'enrollment_id' => $this->enrollment_id,
            'method' => $this->method,
            'reference' => $this->reference,
            'status' => $this->status,

            // ✅ توحيد صيغة التواريخ
            'payment_date' => $this->payment_date
                ? Carbon::parse($this->payment_date)->format('Y-m-d')
                : null,

            'due_date' => $this->due_date
                ? Carbon::parse($this->due_date)->format('Y-m-d')
                : null,

            'final_amount' => $this->amount,

            // ✅ بس إذا في منحة
            $this->mergeWhen(
                $this->scholarship_percentage && $this->scholarship_percentage > 0,
                [
                    'original_amount' => $this->original_amount,
                    'scholarship_percentage' => $this->scholarship_percentage,
                ]
            ),

            'created_at' => $this->created_at
                ? $this->created_at->format('Y-m-d H:i:s')
                : null,

            'created_at_human' => $this->created_at
                ? $this->created_at->diffForHumans()
                : null,

            'updated_at' => $this->updated_at
                ? $this->updated_at->format('Y-m-d H:i:s')
                : null,
        ];
    }
}
