@extends('backend.layouts.master')

@section('title', $pageTitle)

@section('body')
    @php
        $isEdit = filled($record);
        $singular = \Illuminate\Support\Str::headline(\Illuminate\Support\Str::singular($resource));
    @endphp

    <div class="page-heading">
        <h1>{{ $pageTitle }}</h1>
        <p>{{ $isEdit ? 'Update' : 'Create' }} {{ strtolower($singular) }} details for your Arete admin panel.</p>
    </div>

    <article class="panel form-panel {{ in_array($resource, ['products', 'categories'], true) ? 'form-panel-full' : '' }}">
        <form action="{{ $isEdit ? route('backend.resource.update', ['resource' => $resource, 'id' => $record->id]) : route('backend.resource.store', ['resource' => $resource]) }}" method="POST" class="admin-form" enctype="multipart/form-data">
            @csrf
            @if ($isEdit)
                @method('PUT')
            @endif

            @if ($resource === 'products')
                <div class="form-grid">
                    <label class="field-medium">Product Name
                        <input name="name" value="{{ old('name', $record->name ?? '') }}" required>
                        @error('name')<span>{{ $message }}</span>@enderror
                    </label>
                    <label>Slug
                        <input name="slug" value="{{ old('slug', $record->slug ?? '') }}" placeholder="Auto generated if empty">
                        @error('slug')<span>{{ $message }}</span>@enderror
                    </label>
                    <label>SKU
                        <input name="sku" value="{{ old('sku', $record->sku ?? '') }}" required>
                        @error('sku')<span>{{ $message }}</span>@enderror
                    </label>
                    <label>Category
                        <select name="category_id">
                            <option value="">No category</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}" @selected((string) old('category_id', $record->category_id ?? '') === (string) $category->id)>{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </label>
                    <label>Price
                        <input type="number" step="0.01" min="0" name="price" value="{{ old('price', $record->price ?? '') }}" required>
                        @error('price')<span>{{ $message }}</span>@enderror
                    </label>
                    <label>Sale Price
                        <input type="number" step="0.01" min="0" name="sale_price" value="{{ old('sale_price', $record->sale_price ?? '') }}">
                    </label>
                    <label>Stock
                        <input type="number" min="0" name="stock" value="{{ old('stock', $record->stock ?? 0) }}" required>
                    </label>
                    <label>Status
                        <select name="status" required>
                            <option value="active" @selected(old('status', $record->status ?? 'active') === 'active')>Active</option>
                            <option value="inactive" @selected(old('status', $record->status ?? '') === 'inactive')>Inactive</option>
                        </select>
                    </label>
                    <label>Product Image
                        <input type="file" name="image_file" accept="image/png,image/jpeg,image/webp">
                        @error('image_file')<span>{{ $message }}</span>@enderror
                    </label>
                    <label>Test Report Image
                        <input type="file" name="test_report_image_file" accept="image/png,image/jpeg,image/webp">
                        @error('test_report_image_file')<span>{{ $message }}</span>@enderror
                    </label>
                    <div class="image-preview-grid wide">
                        @if ($isEdit && filled($record->image))
                            <div class="image-preview-card">
                                <img src="{{ url($record->image) }}" alt="{{ $record->name }} image">
                                <label class="switch-row">
                                    <input type="checkbox" name="remove_image" value="1">
                                    <span>Remove product image</span>
                                </label>
                            </div>
                        @endif

                        @if ($isEdit && filled($record->test_report_image))
                            <div class="image-preview-card">
                                <img src="{{ url($record->test_report_image) }}" alt="{{ $record->name }} test report image">
                                <label class="switch-row">
                                    <input type="checkbox" name="remove_test_report_image" value="1">
                                    <span>Remove test report</span>
                                </label>
                            </div>
                        @endif
                    </div>
                    <label class="field-medium">Short Description
                        <input name="short_description" value="{{ old('short_description', $record->short_description ?? '') }}">
                    </label>
                    <label class="wide">Description
                        <textarea name="description" rows="4">{{ old('description', $record->description ?? '') }}</textarea>
                    </label>
                    <label class="switch-row">
                        <input type="checkbox" name="is_featured" value="1" @checked(old('is_featured', $record->is_featured ?? false))>
                        <span>Featured product</span>
                    </label>
                </div>
            @elseif ($resource === 'categories')
                <div class="form-grid">
                    <label class="field-medium">Name
                        <input name="name" value="{{ old('name', $record->name ?? '') }}" required>
                        @error('name')<span>{{ $message }}</span>@enderror
                    </label>
                    <label>Slug
                        <input name="slug" value="{{ old('slug', $record->slug ?? '') }}" placeholder="Auto generated if empty">
                        @error('slug')<span>{{ $message }}</span>@enderror
                    </label>
                    <label>Sort Order
                        <input type="number" min="0" name="sort_order" value="{{ old('sort_order', $record->sort_order ?? 0) }}" required>
                    </label>
                    <label>Status
                        <select name="status" required>
                            <option value="active" @selected(old('status', $record->status ?? 'active') === 'active')>Active</option>
                            <option value="inactive" @selected(old('status', $record->status ?? '') === 'inactive')>Inactive</option>
                        </select>
                    </label>
                    <label>Upload Image
                        <input type="file" name="image_file" accept="image/png,image/jpeg,image/webp">
                        @error('image_file')<span>{{ $message }}</span>@enderror
                    </label>
                    <label>Image Path
                        <input name="image" value="{{ old('image', $record->image ?? '') }}">
                    </label>
                    @if ($isEdit && filled($record->image))
                        <div class="image-preview-card wide">
                            <img src="{{ url($record->image) }}" alt="{{ $record->name }} image">
                            <label class="switch-row">
                                <input type="checkbox" name="remove_image" value="1">
                                <span>Remove current image</span>
                            </label>
                        </div>
                    @endif
                    <label class="wide">Description
                        <textarea name="description" rows="4">{{ old('description', $record->description ?? '') }}</textarea>
                    </label>
                </div>
            @elseif ($resource === 'reviews')
                <div class="form-grid">
                    <label class="field-medium">Customer Name
                        <input name="customer_name" value="{{ old('customer_name', $record->customer_name ?? '') }}" required>
                        @error('customer_name')<span>{{ $message }}</span>@enderror
                    </label>
                    <label class="field-medium">Customer Title
                        <input name="customer_title" value="{{ old('customer_title', $record->customer_title ?? '') }}" placeholder="Fitness Coach, Athlete, Customer...">
                        @error('customer_title')<span>{{ $message }}</span>@enderror
                    </label>
                    <label>Product
                        <select name="product_id">
                            <option value="">No product</option>
                            @foreach ($products ?? [] as $product)
                                <option value="{{ $product->id }}" @selected((string) old('product_id', $record->product_id ?? '') === (string) $product->id)>{{ $product->name }}</option>
                            @endforeach
                        </select>
                    </label>
                    <label>Rating
                        <select name="rating" required>
                            @for ($rating = 5; $rating >= 1; $rating--)
                                <option value="{{ $rating }}" @selected((int) old('rating', $record->rating ?? 5) === $rating)>{{ $rating }} Star{{ $rating > 1 ? 's' : '' }}</option>
                            @endfor
                        </select>
                    </label>
                    <label>Status
                        <select name="status" required>
                            <option value="active" @selected(old('status', $record->status ?? 'active') === 'active')>Active</option>
                            <option value="inactive" @selected(old('status', $record->status ?? '') === 'inactive')>Inactive</option>
                        </select>
                    </label>
                    <label>Avatar Path
                        <input name="avatar" value="{{ old('avatar', $record->avatar ?? '') }}" placeholder="frontend/assets/images/testimonials/name.png">
                        @error('avatar')<span>{{ $message }}</span>@enderror
                    </label>
                    <label class="wide">Review
                        <textarea name="comment" rows="5" required>{{ old('comment', $record->comment ?? '') }}</textarea>
                        @error('comment')<span>{{ $message }}</span>@enderror
                    </label>
                    <label class="switch-row">
                        <input type="checkbox" name="is_featured" value="1" @checked(old('is_featured', $record->is_featured ?? false))>
                        <span>Featured review</span>
                    </label>
                </div>
            @else
                <div class="form-grid">
                    <label>Name
                        <input name="name" value="{{ old('name', $record->name ?? '') }}" required>
                        @error('name')<span>{{ $message }}</span>@enderror
                    </label>
                    <label>Email
                        <input type="email" name="email" value="{{ old('email', $record->email ?? '') }}" required>
                        @error('email')<span>{{ $message }}</span>@enderror
                    </label>
                    <label>Phone
                        <input name="phone" value="{{ old('phone', $record->phone ?? '') }}">
                    </label>
                    <label>Role
                        <select name="role" required>
                            @php($currentRole = strtolower(old('role', $record->role ?? 'admin')))
                            <option value="admin" @selected(! str_contains($currentRole, 'super'))>Admin</option>
                            <option value="superadmin" @selected(str_contains($currentRole, 'super'))>Super Admin</option>
                        </select>
                    </label>
                    <label>Status
                        <select name="status" required>
                            <option value="active" @selected(old('status', $record->status ?? 'active') === 'active')>Active</option>
                            <option value="inactive" @selected(old('status', $record->status ?? '') === 'inactive')>Inactive</option>
                        </select>
                    </label>
                    <label>Password
                        <input type="password" name="password" placeholder="{{ $isEdit ? 'Leave blank to keep current password' : 'Minimum 6 characters' }}" @required(! $isEdit)>
                        @error('password')<span>{{ $message }}</span>@enderror
                    </label>
                </div>
            @endif

            <div class="form-actions">
                <a href="{{ route('backend.page', $resource) }}">Cancel</a>
                <button type="submit"><i class="fa-solid fa-floppy-disk"></i>{{ $isEdit ? 'Update' : 'Save' }} {{ $singular }}</button>
            </div>
        </form>
    </article>
@endsection
