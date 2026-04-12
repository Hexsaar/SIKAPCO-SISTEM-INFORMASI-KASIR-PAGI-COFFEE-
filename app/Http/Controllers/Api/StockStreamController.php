<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class StockStreamController extends Controller
{
    public function stream(Request $request)
    {
        $headers = [
            'Content-Type' => 'text/event-stream',
            'Cache-Control' => 'no-cache',
            'Connection' => 'keep-alive',
            'X-Accel-Buffering' => 'no',
        ];

        return response()->stream(function () {
            // Send initial connection message
            echo "data: " . json_encode([
                'type' => 'connection',
                'message' => 'Connected to stock stream',
                'server' => request()->getHost() . ':' . request()->getPort()
            ]) . "\n\n";
            @ob_flush();
            flush();

            $stockFile = storage_path('app/stock_updates.json');

            // Start from current mtime to avoid replaying stale update on new connection.
            $lastFileModified = file_exists($stockFile) ? filemtime($stockFile) : 0;
            $lastHeartbeatAt = time();

            while (true) {
                if (connection_aborted()) {
                    break;
                }

                if (file_exists($stockFile)) {
                    $fileModified = filemtime($stockFile);

                    if ($fileModified > $lastFileModified) {
                        $rawPayload = file_get_contents($stockFile);
                        $updateData = $rawPayload ? json_decode($rawPayload, true) : null;

                        if ($updateData && isset($updateData['type']) && $updateData['type'] === 'stock_update') {
                            echo "data: " . json_encode($updateData) . "\n\n";
                            @ob_flush();
                            flush();
                        }

                        $lastFileModified = $fileModified;
                    }
                }

                // Keep connection alive across proxies/load balancers.
                if ((time() - $lastHeartbeatAt) >= 25) {
                    echo ": keep-alive\n\n";
                    @ob_flush();
                    flush();
                    $lastHeartbeatAt = time();
                }

                // Lower loop pressure to reduce CPU usage while keeping updates responsive.
                usleep(250000);
            }
        }, 200, $headers);
    }

    private function getCurrentStockState()
    {
        return Product::select('id', 'stock', 'is_available')
            ->get()
            ->mapWithKeys(function ($product) {
                return [$product->id => [
                    'stock' => $product->stock,
                    'is_available' => $product->is_available
                ]];
            })
            ->toArray();
    }

    private function detectChanges($oldState, $newState)
    {
        $changes = [];

        foreach ($newState as $productId => $newData) {
            if (!isset($oldState[$productId])) {
                // New product
                $changes[] = [
                    'product_id' => $productId,
                    'stock' => $newData['stock'],
                    'is_available' => $newData['is_available']
                ];
            } elseif ($oldState[$productId]['stock'] !== $newData['stock'] || 
                     $oldState[$productId]['is_available'] !== $newData['is_available']) {
                // Product changed
                $changes[] = [
                    'product_id' => $productId,
                    'stock' => $newData['stock'],
                    'is_available' => $newData['is_available']
                ];
            }
        }

        return $changes;
    }

    public function triggerUpdate(Request $request)
    {
        $productId = $request->input('product_id');
        $product = Product::find($productId);

        if (!$product) {
            return response()->json(['success' => false, 'message' => 'Product not found'], 404);
        }

        // Broadcast stock update to all connected clients
        $this->broadcastStockUpdate($product);

        return response()->json(['success' => true, 'message' => 'Stock update triggered']);
    }

    private function broadcastStockUpdate($product)
    {
        // This would be implemented with a proper broadcasting system
        // For now, we'll just log the update
        \Log::info("Stock update broadcasted for product {$product->id}: {$product->stock}");
    }
}
