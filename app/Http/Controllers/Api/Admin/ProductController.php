<?php

namespace App\Http\Controllers\Api\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Traits\ApiResponses;
use App\Models\Product;
use Illuminate\Support\Facades\Validator;
use App\Models\Category;
use App\Models\CategoryGroup;
use App\Models\User;
use App\Models\Shop;
use Illuminate\Support\Facades\Mail;
use App\Models\ProductMedia;
use Illuminate\Support\Facades\File;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    use ApiResponses;

    public function index($shop_id)
    {
        $products = Product::where('shop_id', $shop_id)->orderBy('id', 'desc')->get();

        return $this->success('Products retrieved successfully.', $products);
    }

    public function restore($id)
    {
        $product = Product::onlyTrashed()->find($id);

        if (!$product) {
            return $this->error('Product Not Found.', ['error' => 'Product Not Found']);
        }
        $product->restore();

        return $this->success('Product restored successfully!', $product);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'shop_id' => 'required|exists:shops,id',
            'name' => 'required|string',
            'deal_type' => 'required|integer|in:1,2,3',
            'category_id' => 'required|exists:categories,id',
            'brand' => 'nullable|string',
            'description' => 'nullable|string',
            'slug' => 'required|string|unique:products,slug',
            'coupon_code' => 'required|string',
            'original_price' => 'required|numeric|min:0',
            'discounted_price' => 'required|numeric|min:0',
            'discount_percentage' => 'required|numeric|min:0|max:100',
            'stock' => 'nullable|integer|min:0',
            'sku' => 'nullable|string|max:100',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
            'specifications' => 'nullable',
            'varient' => 'nullable',
        ], [
            'name.required' => 'The name field is required.',
            'shop_id.required' => 'Please provide the shop for this product.',
            'shop_id.exists' => 'The selected shop does not exist in our records.',
            'deal_type.required' => 'Please specify the deal type for this product.',
            'deal_type.integer' => 'The deal type must be an integer value.',
            'deal_type.in' => 'The deal type must be either 0 (standard) or 1 (special deal).',
            'category_id.required' => 'Please select a category for this product.',
            'category_id.exists' => 'The selected category does not exist in our records.',
            'brand.string' => 'The brand must be a valid string.',
            'description.string' => 'The description must be a valid string.',
            'slug.required' => 'The product slug is required.',
            'slug.string' => 'The slug must be a valid string.',
            'slug.unique' => 'The product slug has already been taken.',
            'coupon_code.required' => 'The product coupon code is required.',
            'coupon_code.string' => 'The coupon code must be a valid string.',
            'original_price.required' => 'Please provide the original price of the product.',
            'original_price.numeric' => 'The original price must be a valid number.',
            'original_price.min' => 'The original price must be at least 0.',
            'discounted_price.required' => 'Please provide the discounted price of the product.',
            'discounted_price.numeric' => 'The discounted price must be a valid number.',
            'discounted_price.min' => 'The discounted price must be at least 0.',
            'discount_percentage.required' => 'Please provide the discount percentage for this product.',
            'discount_percentage.numeric' => 'The discount percentage must be a valid number.',
            'discount_percentage.min' => 'The discount percentage cannot be negative.',
            'discount_percentage.max' => 'The discount percentage cannot exceed 100.',
            'stock.required' => 'Please provide the stock quantity for this product.',
            'stock.integer' => 'The stock must be a valid integer value.',
            'stock.min' => 'The stock cannot be negative.',
            'sku.string' => 'The SKU must be a valid string.',
            'sku.unique' => 'The SKU has already been taken.',
            'sku.max' => 'The SKU must not exceed 100 characters.',
            'start_date.date' => 'The start date must be a valid date format.',
            'end_date.date' => 'The end date must be a valid date format.',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $validatedData = $validator->validated();
        $shopId = $request->input('shop_id');

        if (!empty($validatedData['sku'])) {
            $originalSku = $validatedData['sku'];
            $counter = 1;

            while (Product::where('sku', $validatedData['sku'])->exists()) {
                $validatedData['sku'] = $originalSku . '-' . $counter;
                $counter++;
            }
        }

        $validatedData['active'] = 1;
        $validatedData['specifications'] = $request->specifications;
        $validatedData['varient'] = $request->varient;
        $validatedData['delivery_days'] = $request->delivery_days;

        $product = Product::create($validatedData);

        $images = $request->media;
        $video_urls = $request->media_url;
        if ($images) {
            $slug = Str::slug($product->slug, '_');

            $publicPath = "assets/products/images/" . $shopId . "/" . $slug;

            if (!File::exists($publicPath)) {
                try {
                    File::makeDirectory($publicPath, 0777, true, true);
                } catch (\Exception $e) {
                    return response()->json(['error' => 'Failed to create directory: ' . $e->getMessage()], 500);
                }
            }

            $imageManager = new ImageManager(new Driver());

            foreach ($images as $order => $image) {
                $originalImageName = time() . '_' . $image->getClientOriginalName();
                $resizedImageName = time() . '_resize_' . pathinfo($originalImageName, PATHINFO_FILENAME) . '.webp';

                $image->move($publicPath, $originalImageName);

                try {
                    $imageInstance = $imageManager->read($publicPath . '/' . $originalImageName);
                    $imageInstance->cover(320, 240)
                        ->toWebp(90)
                        ->save($publicPath . '/' . $resizedImageName);

                    $product->productMedia()->create([
                        'path' => $publicPath . '/' . $originalImageName,
                        'resize_path' => $publicPath . '/' . $resizedImageName,
                        'order' => $order,
                        'type' => 'image',
                        'imageable_id' => $product->id,
                        'imageable_type' => get_class($product)
                    ]);
                } catch (\Exception $e) {
                    return response()->json(['error' => 'Error processing image: ' . $e->getMessage()], 500);
                }
            }
        }

        if ($video_urls) {
            foreach ($video_urls as $order => $video_url) {
                $product->productMedia()->create([
                    'path' => $video_url,
                    'resize_path' =>  $video_url,
                    'order' => $order,
                    'type' => 'video',
                    'imageable_id' => $product->id,
                    'imageable_type' => get_class($product)
                ]);
            }
        }

        return $this->success('Product Created Successfully!', $product);
    }

    public function show($id)
    {
        $product = Product::with(['category', 'category.categoryGroup', 'productMedia:id,resize_path,order,type,imageable_id'])->find($id);

        if (!$product) {
            return $this->error('Product Not Found.', ['error' => 'Product Not Found']);
        }

        $product->categoryName = $product->category ? $product->category->name : null;
        $product->categoryGroupName = $product->category && $product->category->categoryGroup ? $product->category->categoryGroup->name : null;
        $product->categoryGroupId = $product->category && $product->category->categoryGroup ? $product->category->categoryGroup->id : null;

        unset($product->category);

        return $this->success('Product Retrieved Successfully!', $product);
    }

    public function update(Request $request, $id)
    {
        // Validate input data
        $validator = Validator::make($request->all(), [
            'shop_id' => 'required|exists:shops,id',
            'name' => 'required|string',
            'deal_type' => 'required|integer|in:1,2,3',
            'category_id' => 'required|exists:categories,id',
            'brand' => 'nullable|string',
            'description' => 'nullable|string',
            'slug' => 'required|string|unique:products,slug,' . $id,
            'coupon_code' => 'required|string',
            'original_price' => 'required|numeric|min:0',
            'discounted_price' => 'required|numeric|min:0',
            'discount_percentage' => 'required|numeric|min:0|max:100',
            'stock' => 'nullable|integer|min:0',
            'sku' => 'nullable|string|max:100|unique:products,sku,' . $id, // Ensure SKU uniqueness
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
            'specifications' => 'nullable',
            'varient' => 'nullable',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Find product
        $product = Product::find($id);
        if (!$product) {
            return response()->json(['error' => 'Product Not Found.'], 404);
        }

        // Update product details
        $validatedData = $validator->validated();
        $validatedData['specifications'] = $request->specifications;
        $validatedData['varient'] = $request->varient;
        $validatedData['delivery_days'] = $request->delivery_days;

        try {
            $product->update($validatedData);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error updating product: ' . $e->getMessage()], 500);
        }

        // Handle media uploads
        if ($request->hasFile('media')) {
            $images = $request->file('media');
            $shopId = $request->input('shop_id');
            $publicPath = "assets/products/images/" . $shopId . "/" . $product->slug;

            // Ensure directory exists
            if (!File::exists($publicPath)) {
                File::makeDirectory($publicPath, 0777, true, true);
            }

            $imageManager = new ImageManager(new Driver());

            foreach ($images as $order => $image) {
                $originalImageName = time() . '_' . $image->getClientOriginalName();
                $resizedImageName = time() . '_resize_' . pathinfo($originalImageName, PATHINFO_FILENAME) . '.webp';

                // Move original image
                $image->move($publicPath, $originalImageName);

                try {
                    // Resize image
                    $imageInstance = $imageManager->read($publicPath . '/' . $originalImageName);
                    $imageInstance->cover(320, 240)
                        ->toWebp(90)
                        ->save($publicPath . '/' . $resizedImageName);

                    // Delete existing image (if any)
                    $existingMedia = $product->productMedia()->where('order', $order)->first();
                    if ($existingMedia) {
                        if (File::exists($existingMedia->path)) File::delete($existingMedia->path);
                        if (File::exists($existingMedia->resize_path)) File::delete($existingMedia->resize_path);
                        $existingMedia->delete();
                    }

                    // Store new image in database
                    $product->productMedia()->create([
                        'path' => $publicPath . '/' . $originalImageName,
                        'resize_path' => $publicPath . '/' . $resizedImageName,
                        'order' => $order,
                        'type' => 'image',
                        'imageable_id' => $product->id,
                        'imageable_type' => get_class($product)
                    ]);
                } catch (\Exception $e) {
                    return response()->json(['error' => 'Error processing image: ' . $e->getMessage()], 500);
                }
            }
        }

        // Handle video URLs
        if ($request->has('media_url')) {
            $video_urls = $request->input('media_url');

            foreach ($video_urls as $order => $video_url) {
                $existingMedia = $product->productMedia()->where('order', $order)->first();

                // Delete existing media if replacing with a video
                if ($existingMedia && $existingMedia->type === 'image') {
                    if (File::exists($existingMedia->path)) File::delete($existingMedia->path);
                    if (File::exists($existingMedia->resize_path)) File::delete($existingMedia->resize_path);
                    $existingMedia->delete();
                }

                $product->productMedia()->updateOrCreate(
                    ['order' => $order],
                    [
                        'path' => $video_url,
                        'resize_path' => $video_url,
                        'order' => $order,
                        'type' => 'video',
                        'imageable_id' => $product->id,
                        'imageable_type' => get_class($product)
                    ]
                );
            }
        }

        return response()->json(['message' => 'Product Updated Successfully!', 'product' => $product], 200);
    }

    public function destroy(string $id)
    {
        $products = Product::findOrFail($id);

        $products->delete();

        return $this->ok('Product Deleted Successfully!');
    }

    public function destroyProductMedia(string $id)
    {
        $productMedia = ProductMedia::findOrFail($id);

        if (file_exists($productMedia->path)) {
            unlink($productMedia->path);
        }

        if (file_exists($productMedia->resize_path)) {
            unlink($productMedia->resize_path);
        }

        $productMedia->delete();

        return $this->ok('Product Media Deleted Successfully!');
    }

    public function getAllCategoryGroups()
    {
        $categoryGroups = CategoryGroup::where('active', 1)
            ->select('id', 'name')
            ->orderBy('id', 'desc')
            ->get();

        return $this->success('Active Category Groups Retrieved Successfully!', $categoryGroups);
    }

    public function getAllCategoriesByCategoryGroupId($id)
    {
        $categories = Category::where('category_group_id', $id)
            ->where('active', 1)
            ->select('id', 'name')
            ->orderBy('id', 'desc')
            ->get();
        return $this->success('Active Categories Retrieved Successfully!', $categories);
    }

    public function categoriesCreate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'category_group_id' => 'required|exists:category_groups,id',
            'name' => 'required|string|max:200|unique:categories,name',
            'slug' => 'required|string|max:200|unique:categories,slug',
            'description' => 'nullable|string',
            'icon' => 'required|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
        ], [
            'category_group_id.required' => 'The category group ID field is required.',
            'category_group_id.exists' => 'The selected category group ID is invalid.',
            'name.required' => 'The name field is required.',
            'name.unique' => 'The name field must be unique.',
            'name.max' => 'The name may not be greater than 200 characters.',
            'slug.required' => 'The slug field is required.',
            'slug.max' => 'The slug may not be greater than 200 characters.',
            'slug.unique' => 'The slug must be unique.',
            'description.string' => 'The description must be a string.',
            'icon.required' => 'The icon is required.',
            'icon.image' => 'The icon must be an image.',
            'icon.mimes' => 'The icon must be a jpeg, png, jpg, gif, svg, or webp file.',
            'icon.max' => 'The icon must not be larger than 2MB.',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $validatedData = $validator->validated();

        if ($request->hasFile('icon')) {
            $image = $request->file('icon');
            $imagePath = 'assets/images/categories';

            if (!file_exists(public_path($imagePath))) {
                mkdir(public_path($imagePath), 0755, true);
            }

            $imageName = time() . '_' . $image->getClientOriginalName();

            $image->move($imagePath, $imageName);

            $validatedData['icon'] = $imagePath . '/' . $imageName;
            $validatedData['active'] = 0;
        }

        $category = Category::create($validatedData);

        return $this->success('Category created successfully! It will be available once approved by admin. Please try creating a product after some time.', $category);
    }
}
