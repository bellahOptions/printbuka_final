<?php

namespace App\Livewire\Product;

use App\Services\ProductSuggestionService;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class Suggestions extends Component
{
    public ?int $excludeCatalogId = null;

    public ?int $excludeShopId = null;

    public function render(): View
    {
        $service = app(ProductSuggestionService::class);

        $result = $service->getSuggestions(
            catalogLimit: 4,
            shopLimit: 2,
            excludeCatalogId: $this->excludeCatalogId,
            excludeShopId: $this->excludeShopId,
        );

        return view('livewire.product.suggestions', [
            'catalogProducts' => $result['catalog'],
            'shopProducts'    => $result['shop'],
            'hasHistory'      => $result['has_history'],
            'personalized'    => $result['personalized'],
        ]);
    }
}
