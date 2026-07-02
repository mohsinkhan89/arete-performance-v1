@extends('backend.layouts.master')

@section('title', $pageTitle)

@section('body')
    <div class="page-heading">
        <h1>{{ $pageTitle }}</h1>
        <p>Manage {{ strtolower($pageTitle) }} from your Arete Performance admin panel.</p>
    </div>

    @if (in_array($page, ['products', 'categories', 'users', 'reviews'], true))
        @php
            $canManage = $page !== 'users' || $canManageUsers;
        @endphp
        <article class="panel resource-panel">
            <div class="panel-head">
                <h2>{{ $pageTitle }} Table</h2>
                @if ($canManage && in_array($page, ['products', 'categories', 'users'], true))
                    <a href="{{ route('backend.resource.create', ['resource' => $page]) }}"><i class="fa-solid fa-plus"></i> Add New</a>
                @endif
            </div>

            <div class="table-wrap">
                @if ($page === 'products')
                    <table>
                        <thead>
                            <tr><th>Product</th><th>Category</th><th>SKU</th><th>Price</th><th>Stock</th><th>Status</th><th>Action</th></tr>
                        </thead>
                        <tbody>
                            @forelse ($records as $product)
                                <tr>
                                    <td>
                                        <span class="table-media">
                                            <img src="{{ url($product->image ?: 'backend/assets/imgs/product-bottle.png') }}" alt="{{ $product->name }}">
                                            {{ $product->name }}
                                        </span>
                                    </td>
                                    <td>{{ $product->category?->name ?? '-' }}</td>
                                    <td>{{ $product->sku }}</td>
                                    <td>£{{ number_format((float) $product->price, 2) }}</td>
                                    <td>{{ $product->stock }}</td>
                                    <td>
                                        <form action="{{ route('backend.resource.status', ['resource' => 'products', 'id' => $product->id]) }}" method="POST">
                                            @csrf
                                            @method('PATCH')
                                            <button class="status-toggle {{ $product->status === 'active' ? 'is-active' : 'is-inactive' }}" type="submit" title="Toggle status">
                                                <i class="fa-solid {{ $product->status === 'active' ? 'fa-toggle-on' : 'fa-toggle-off' }}"></i>
                                                {{ ucfirst($product->status) }}
                                            </button>
                                        </form>
                                    </td>
                                    <td>
                                        <div class="action-group">
                                            <a href="{{ route('backend.resource.edit', ['resource' => 'products', 'id' => $product->id]) }}" title="Edit"><i class="fa-solid fa-pen"></i></a>
                                            <form action="{{ route('backend.resource.destroy', ['resource' => 'products', 'id' => $product->id]) }}" method="POST" onsubmit="return confirm('Delete this product?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" title="Delete"><i class="fa-solid fa-trash"></i></button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="7" class="empty-cell">No products found.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                @elseif ($page === 'categories')
                    <table>
                        <thead>
                            <tr><th>Image</th><th>Name</th><th>Slug</th><th>Products</th><th>Sort</th><th>Status</th><th>Created</th><th>Action</th></tr>
                        </thead>
                        <tbody>
                            @forelse ($records as $category)
                                <tr>
                                    <td><img class="category-thumb" src="{{ url($category->image ?: 'backend/assets/imgs/product-bottle.png') }}" alt="{{ $category->name }}"></td>
                                    <td>{{ $category->name }}</td>
                                    <td>{{ $category->slug }}</td>
                                    <td>{{ $category->products_count }}</td>
                                    <td>{{ $category->sort_order }}</td>
                                    <td>
                                        <form action="{{ route('backend.resource.status', ['resource' => 'categories', 'id' => $category->id]) }}" method="POST">
                                            @csrf
                                            @method('PATCH')
                                            <button class="status-toggle {{ $category->status === 'active' ? 'is-active' : 'is-inactive' }}" type="submit" title="Toggle status">
                                                <i class="fa-solid {{ $category->status === 'active' ? 'fa-toggle-on' : 'fa-toggle-off' }}"></i>
                                                {{ ucfirst($category->status) }}
                                            </button>
                                        </form>
                                    </td>
                                    <td>{{ $category->created_at?->format('M d, Y') }}</td>
                                    <td>
                                        <div class="action-group">
                                            <a href="{{ route('backend.resource.edit', ['resource' => 'categories', 'id' => $category->id]) }}" title="Edit"><i class="fa-solid fa-pen"></i></a>
                                            <form action="{{ route('backend.resource.destroy', ['resource' => 'categories', 'id' => $category->id]) }}" method="POST" onsubmit="return confirm('Delete this category?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" title="Delete"><i class="fa-solid fa-trash"></i></button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="8" class="empty-cell">No categories found.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                @elseif ($page === 'users')
                    <table>
                        <thead>
                            <tr><th>User</th><th>Email</th><th>Phone</th><th>Role</th><th>Status</th><th>Joined</th><th>Action</th></tr>
                        </thead>
                        <tbody>
                            @forelse ($records as $user)
                                <tr>
                                    <td>{{ $user->name }}</td>
                                    <td>{{ $user->email }}</td>
                                    <td>{{ $user->phone ?? '-' }}</td>
                                    <td>{{ ucfirst($user->role ?? 'user') }}</td>
                                    <td>
                                        @if ($canManageUsers && auth()->id() !== $user->id)
                                            <form action="{{ route('backend.resource.status', ['resource' => 'users', 'id' => $user->id]) }}" method="POST">
                                                @csrf
                                                @method('PATCH')
                                                <button class="status-toggle {{ ($user->status ?? 'active') === 'active' ? 'is-active' : 'is-inactive' }}" type="submit" title="Toggle status">
                                                    <i class="fa-solid {{ ($user->status ?? 'active') === 'active' ? 'fa-toggle-on' : 'fa-toggle-off' }}"></i>
                                                    {{ ucfirst($user->status ?? 'active') }}
                                                </button>
                                            </form>
                                        @else
                                            <span class="badge {{ ($user->status ?? 'active') === 'active' ? 'green' : 'red' }}">{{ ucfirst($user->status ?? 'active') }}</span>
                                        @endif
                                    </td>
                                    <td>{{ $user->created_at?->format('M d, Y') }}</td>
                                    <td>
                                        @if ($canManageUsers)
                                            <div class="action-group">
                                                <a href="{{ route('backend.resource.edit', ['resource' => 'users', 'id' => $user->id]) }}" title="Edit"><i class="fa-solid fa-pen"></i></a>
                                                @if (auth()->id() !== $user->id)
                                                    <form action="{{ route('backend.resource.destroy', ['resource' => 'users', 'id' => $user->id]) }}" method="POST" onsubmit="return confirm('Delete this user?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" title="Delete"><i class="fa-solid fa-trash"></i></button>
                                                    </form>
                                                @endif
                                            </div>
                                        @else
                                            <span class="view-only"><i class="fa-regular fa-eye"></i> View only</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="7" class="empty-cell">No users found.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                @else
                    <table>
                        <thead>
                            <tr><th>Customer</th><th>Product</th><th>Rating</th><th>Review</th><th>Status</th><th>Created</th></tr>
                        </thead>
                        <tbody>
                            @forelse ($records as $review)
                                <tr>
                                    <td>
                                        <span class="table-media">
                                            <img src="{{ url($review->avatar ?: 'frontend/assets/images/testimonials/miker.png') }}" alt="{{ $review->customer_name }}">
                                            {{ $review->customer_name }}
                                        </span>
                                    </td>
                                    <td>{{ $review->product?->name ?? '-' }}</td>
                                    <td><span class="rating-stars">{{ str_repeat('★', $review->rating) }}</span></td>
                                    <td>{{ \Illuminate\Support\Str::limit($review->comment, 70) }}</td>
                                    <td><span class="badge {{ $review->status === 'active' ? 'green' : 'red' }}">{{ ucfirst($review->status) }}</span></td>
                                    <td>{{ $review->created_at?->format('M d, Y') }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="6" class="empty-cell">No reviews found.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                @endif
            </div>

            <div class="pagination-row">
                {{ $records->links() }}
            </div>
        </article>
    @else
        <article class="panel empty-page">
            <i class="fa-solid fa-layer-group"></i>
            <h2>{{ $pageTitle }} Page</h2>
            <p>This backend page is ready for your dynamic content, forms, and tables.</p>
        </article>
    @endif
@endsection

