<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class SubscriptionController extends Controller
{
    private string $apiUrl = "http://127.0.0.1:8001/api/subscriptions";
    private string $customersApiUrl = "http://127.0.0.1:8001/api/customers";
    private string $servicesApiUrl = "http://127.0.0.1:8001/api/services";

    public function index(): View
    {
        $response = Http::get($this->apiUrl);
        $subscriptions = $response->successful() ? $response->json("data") : [];

        $customersResponse = Http::get($this->customersApiUrl, [
            "status" => "active",
        ]);
        $customers = $customersResponse->successful()
            ? $customersResponse->json("data")
            : [];

        $servicesResponse = Http::get($this->servicesApiUrl, [
            "status" => "active",
        ]);
        $services = $servicesResponse->successful()
            ? $servicesResponse->json("data")
            : [];

        return view("subscriptions.index", [
            "active" => "subscriptions",
            "subscriptions" => $subscriptions,
            "customers" => $customers,
            "services" => $services,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $startDate = $request->start_date;
        $endDate = $request->end_date;

        if ($startDate && str_contains($startDate, "/")) {
            $parts = explode("/", $startDate);
            $startDate = "{$parts[2]}-{$parts[1]}-{$parts[0]}";
        }
        if ($endDate && str_contains($endDate, "/")) {
            $parts = explode("/", $endDate);
            $endDate = "{$parts[2]}-{$parts[1]}-{$parts[0]}";
        }

        $response = Http::post($this->apiUrl, [
            "customer_id" => $request->customer_id,
            "service_id" => $request->service_id,
            "start_date" => $startDate,
            "end_date" => $endDate,
            "status" => $request->status,
        ]);

        if ($response->successful()) {
            return redirect()
                ->route("subscriptions.index")
                ->with("toast_success", $response->json("message"));
        }

        if ($response->status() === 422) {
            return back()
                ->withErrors($response->json("errors") ?? [])
                ->withInput()
                ->with("toast_error", $response->json("message"))
                ->with("open_modal", "addDataModal");
        }

        return back()
            ->withInput()
            ->with(
                "toast_error",
                $response->json("message") ?? "Something went wrong",
            );
    }

    public function activate(int $id): RedirectResponse
    {
        $response = Http::patch("{$this->apiUrl}/{$id}/activate");

        if ($response->successful()) {
            return redirect()
                ->route("subscriptions.index")
                ->with("toast_success", $response->json("message"));
        }

        return back()->with(
            "toast_error",
            $response->json("message") ?? "Something went wrong",
        );
    }

    public function deactivate(int $id): RedirectResponse
    {
        $response = Http::patch("{$this->apiUrl}/{$id}/deactivate");

        if ($response->successful()) {
            return redirect()
                ->route("subscriptions.index")
                ->with("toast_success", $response->json("message"));
        }

        return back()->with(
            "toast_error",
            $response->json("message") ?? "Something went wrong",
        );
    }

    public function trial(int $id): RedirectResponse
    {
        $response = Http::patch("{$this->apiUrl}/{$id}/trial");

        if ($response->successful()) {
            return redirect()
                ->route("subscriptions.index")
                ->with("toast_success", $response->json("message"));
        }

        return back()->with(
            "toast_error",
            $response->json("message") ?? "Something went wrong",
        );
    }

    public function isolir(int $id): RedirectResponse
    {
        $response = Http::patch("{$this->apiUrl}/{$id}/isolir");

        if ($response->successful()) {
            return redirect()
                ->route("subscriptions.index")
                ->with("toast_success", $response->json("message"));
        }

        return back()->with(
            "toast_error",
            $response->json("message") ?? "Something went wrong",
        );
    }

    public function dismantle(int $id): RedirectResponse
    {
        $response = Http::patch("{$this->apiUrl}/{$id}/dismantle");

        if ($response->successful()) {
            return redirect()
                ->route("subscriptions.index")
                ->with("toast_success", $response->json("message"));
        }

        return back()->with(
            "toast_error",
            $response->json("message") ?? "Something went wrong",
        );
    }
}