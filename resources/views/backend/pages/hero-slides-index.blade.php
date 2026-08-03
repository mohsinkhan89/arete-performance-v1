@extends('backend.layouts.master')
@section('title', 'Hero Slides')
@section('body')
<div class="page-heading d-flex justify-content-between align-items-end gap-3">
  <div><h1>Hero Slides</h1><p>Manage the homepage hero content and images.</p></div>
  <a class="vx-add-record" href="{{ route('backend.resource.create', 'hero-slides') }}"><i class="fa-solid fa-plus"></i> Add Hero Slide</a>
</div>
<article class="panel">
  <div class="table-wrap">
    <table>
      <thead><tr><th>Preview</th><th>Content</th><th>Order</th><th>Status</th><th>Actions</th></tr></thead>
      <tbody>
      @forelse($records as $slide)
        <tr>
          <td><span class="table-media"><img src="{{ url($slide->product_image ?: 'frontend/assets/images/product-bottle.png') }}" alt="{{ $slide->title }}"></span></td>
          <td><strong>{{ $slide->title }}</strong><br><small>{{ $slide->subtitle ?: 'No subtitle' }}</small></td>
          <td>{{ $slide->sort_order }}</td>
          <td>
            <form class="inline-toggle-form" action="{{ route('backend.resource.status', ['resource'=>'hero-slides','id'=>$slide->id]) }}" method="POST">@csrf @method('PATCH')
              <button class="status-toggle modern-switch {{ $slide->status === 'active' ? 'is-active' : 'is-inactive' }}" type="submit"><i></i><span>{{ ucfirst($slide->status) }}</span></button>
            </form>
          </td>
          <td><div class="vx-row-actions">
            <a href="{{ route('backend.resource.edit', ['resource'=>'hero-slides','id'=>$slide->id]) }}" title="Edit"><i class="fa-regular fa-pen-to-square"></i></a>
            <form action="{{ route('backend.resource.destroy', ['resource'=>'hero-slides','id'=>$slide->id]) }}" method="POST" onsubmit="return confirm('Delete this hero slide?')">@csrf @method('DELETE')<button type="submit" title="Delete"><i class="fa-regular fa-trash-can"></i></button></form>
          </div></td>
        </tr>
      @empty
        <tr><td colspan="5" class="empty-cell">No hero slides yet. Click “Add Hero Slide” to create one.</td></tr>
      @endforelse
      </tbody>
    </table>
  </div>
  <div class="mt-3">{{ $records->links('backend.partials.vuexy-pagination') }}</div>
</article>
@endsection