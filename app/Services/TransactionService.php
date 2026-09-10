<?php

namespace App\Services;

use App\Models\User;
use App\Models\VtuTransaction;
use App\Models\WalletTransaction;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class TransactionService
{
    public function getUserVtuTransactions(User $user, array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = VtuTransaction::where('user_id', $user->id)->latest();

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['service_type'])) {
            $query->where('service_type', $filters['service_type']);
        }

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('reference', 'like', "%{$search}%")
                  ->orWhere('recipient', 'like', "%{$search}%")
                  ->orWhere('provider', 'like', "%{$search}%");
            });
        }

        return $query->paginate($perPage)->withQueryString();
    }

    public function getUserWalletTransactions(User $user, int $perPage = 15): LengthAwarePaginator
    {
        return WalletTransaction::where('user_id', $user->id)->latest()->paginate($perPage);
    }

    public function findByReference(string $reference): ?VtuTransaction
    {
        return VtuTransaction::where('reference', $reference)->first();
    }
}
