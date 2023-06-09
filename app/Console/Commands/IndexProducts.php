<?php

namespace App\Console\Commands;

use App\Models\Product;
use Illuminate\Console\Command;
use Elasticsearch;

class IndexProducts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'index:products';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $products = Product::all();

        foreach ($products as $product) {
            try {
                Elasticsearch::index([
                    'id'    => $product->id,
                    'index' => 'products',
                    'body'  => [
                        'name_product' => $product->name_product,
                        'brand'        => $product->brand,
                        'shop_id'      => $product->shop_id,
                    ]
                ]);
            } catch (Exception $e) {
                $this->info($e->getMessage());
            }
        }

        $this->info("Posts were successfully indexed");
    }
}
