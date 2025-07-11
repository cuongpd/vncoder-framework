@push('topMenu')
    <a href="{{$linkEdit}}" title="Tạo danh mục mới"><button type="button" class="btn btn-sm btn-primary"><i class="fa-duotone fa-solid fa-plus"></i>&nbsp;Thêm mới</button></a>
@endpush

<div class="row">
    <div class="col-sm-12">
        <div class="table-responsive dt-responsive">
            <table id="tableData" class="table table-responsive table-bordered table-striped order-column tr-centered">
                <thead>
                <tr>
                    <th>Name</th>
                    <th class="text-center">Posts</th>
                    <th>Link</th>
                    <th>Description</th>
                    <th>Photo</th>
                    <th class="no-sort text-center" width="200px">Logs</th>
                    <th class="no-sort text-left" width="120px">Action</th>
                </tr>
                </thead>
                @foreach($categoryData as $category)
                    <tr>
                        <td><span class="f-w-600">{{$category->id}} - {{$category->title}}</span></td>
                        <td class="text-center">{{$category->posts->count()}}</td>
                        <td><a href="{{$category->link}}" target="_blank">{{$category->link}}</a></td>
                        <td>{{$category->description}}</td>
                        <td class="text-center">
                            @if($category->photo)
                                <img src="{{url($category->photo)}}" class="photo-thumbnail" style="max-width: 100px;">
                            @endif
                        </td>
                        <td class="text-left" style="font-size: 12px;">
                            <span>C : {{$category->created ?? 'N/A'}}</span><br/>
                            <span>U : {{$category->updated ?? 'N/A'}}</span>
                        </td>
                        <td class="text-left">
                            <a href="{{$linkEdit}}?id={{$category->id}}" class="btn btn-sm text-success" title="Chỉnh sửa danh mục"><i class="fa-duotone fa-solid fa-pen-to-square fa-20"></i></a>
                            <a href="{{$linkAddCategoryTree}}?parent_id={{$category->id}}" class="btn btn-sm text-info" title="Thêm danh mục con"><i class="fa-duotone fa-solid fa-list-tree fa-20"></i></a>
                            @if($category->child->count() == 0 && $category->posts->count() == 0)
                                <a href="{{$linkDelete}}?id={{$category->id}}" onclick="return confirm('Xóa bỏ danh mục {{$category->title}}???');" class="btn btn-sm text-danger"><i class="fa-duotone fa-solid fa-trash-can-xmark fa-20"></i></a>
                            @endif
                        </td>
                    </tr>
                    @if($category->child)
                        @foreach($category->child as $child)
                            <tr>
                                <td>
                                    <span class="p-l-15">&#x2022; {{$child->id}} - {{$child->title}}</span>
                                </td>
                                <td class="text-center">{{$child->posts->count()}}</td>
                                <td><a href="{{$child->link}}" target="_blank">{{$child->link}}</a></td>
                                <td>{{$child->description}}</td>
                                <td class="text-center">
                                    @if($child->photo)
                                        <img src="{{url($child->photo)}}" class="photo-thumbnail" style="max-width: 100px;">
                                    @endif
                                </td>
                                <td class="text-left" style="font-size: 12px;">
                                    <span>C : {{$child->created ?? 'N/A'}}</span><br/>
                                    <span>U : {{$child->updated ?? 'N/A'}}</span>
                                </td>
                                <td class="text-left">
                                    <a href="{{$linkEdit}}?id={{$child->id}}" class="btn btn-sm text-success"><i class="fa-duotone fa-solid fa-pen-to-square fa-20"></i></a>
                                    <a href="{{$linkDelete}}?id={{$child->id}}" onclick="return confirm('Xóa bỏ danh mục {{$child->title}}???');" class="btn btn-sm text-danger"><i class="fa-duotone fa-solid fa-trash-can-xmark fa-20"></i></a>
                                </td>
                            </tr>
                        @endforeach
                    @endif
                @endforeach
            </table>
        </div>
    </div>
</div>