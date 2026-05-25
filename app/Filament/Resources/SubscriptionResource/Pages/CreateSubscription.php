<?php

namespace App\Filament\Resources\SubscriptionResource\Pages;

use App\Filament\Resources\SubscriptionResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Http;
use Filament\Notifications\Notification;
use Carbon\Carbon;

class CreateSubscription extends CreateRecord
{
    protected static string $resource = SubscriptionResource::class;

    protected function handleRecordCreation(array $data): \Illuminate\Database\Eloquent\Model
    {
        try {

            $formattedStartDate = Carbon::parse($data['start_date'])->format('d/m/Y');
            $formattedEndDate = Carbon::parse($data['end_date'])->format('d/m/Y');

            $response = Http::timeout(5)->post("http://127.0.0.1:8001/api/subscriptions", [
                "customer_id" => $data['customer_id'],
                "service_id"  => $data['service_id'],
                "start_date"  => $formattedStartDate,
                "end_date"    => $formattedEndDate,
                "status"      => $data['status'],
            ]);

            if (!$response->successful()) {
                $errorMessage = $response->json('message') ?? 'Gagal mendaftarkan data langganan ke API.';
                Notification::make()->danger()->title('Validasi Gagal')->body($errorMessage)->send();
                $this->halt();
            }

                Notification::make()
                    ->success()
                    ->title('Sukses')
                    ->body($response->json('message') ?? 'Subscription berhasil disimpan.')
                    ->send();

        } catch (\Exception $e) {
            Notification::make()
                ->danger()
                ->title('Koneksi Gagal')
                ->body('Tidak dapat terhubung ke Server API Backend (Port 8001).')
                ->send();
            $this->halt();
        }


        // Menyimpan data asli ke database lokal Frontend Filament dengan format standar Y-m-d
        return static::getModel()::create([
            'customer_id' => $data['customer_id'],
            'service_id'  => $data['service_id'],
            'start_date'  => $data['start_date'],
            'end_date'    => $data['end_date'],
            'status'      => $data['status'],
        ]);
    }
}