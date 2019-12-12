<div class="col-md-12">

              <table class="table table-bordered">
                <tbody>

                @php 
                $i=$object_data->firstItem(); 
                $colspan = ($object_type == 'document')?'4':'5';
                @endphp
                @if($object_type == 'document')
                 <tr>
                  <th>#</th>
                  <th>Select</th>
                  <th>Doc ID</th>
                  <th>Name</th>
                  
                </tr>
                @foreach($object_data as $row)
                @php
                $var = 'data-obj_id='.$row->document_id.'  data-obj_name='.$row->document_name;
                @endphp
                <tr>
                  <td width="2%" nowrap="nowrap">{{$i++}}</td>
                   <td width="3%" nowrap="nowrap" style="text-align: center;">
                  <input type="radio" name="search_object_id"  value="{{$row->document_id}}" {{$var}} class="btn btn-primary btn-xs select_object_item"> 
                  </td>
                  <td><span class="select_object_item" {{$var}} style="cursor: pointer;">{{$row->document_no}}</span></td>
                  <td><span class="select_object_item" {{$var}} style="cursor: pointer;">{{$row->document_name}}</span></td>
                 
                </tr>
                @endforeach
                @endif 

                @if($object_type == 'form')
                 <tr>
                  <th>#</th>
                  <th>Select</th>
                  <th>Form</th>
                  <th>User</th>
                  <th>Date</th>
                  
                </tr>
                @foreach($object_data as $row)
                <tr>
                  <td width="2%" nowrap="nowrap">{{$i++}}</td>
                   <td width="3%" nowrap="nowrap" style="text-align: center;">
                  <input type="radio" name="search_object_id"  value="{{$row->form_response_unique_id}}" data-obj_id="{{$row->form_response_unique_id}}"  data-obj_name="{{$row->form_name}} - {{$row->user_full_name}} - {{$row->created_at}}" class="btn btn-primary btn-xs select_object_item">
                  </td>
                  <td><span class="select_object_item" data-obj_id="{{$row->form_response_unique_id}}" data-obj_name="{{$row->form_name}} - {{$row->user_full_name}} - {{$row->created_at}}" style="cursor: pointer;">{{$row->form_name}}</span></td>
                  <td><span class="select_object_item" data-obj_id="{{$row->form_response_unique_id}}" data-obj_name="{{$row->form_name}} - {{$row->user_full_name}} - {{$row->created_at}}" style="cursor: pointer;">{{$row->user_full_name}}</span></td>
                 <td><span class="select_object_item" data-obj_id="{{$row->form_response_unique_id}}" data-obj_name="{{$row->form_name}} - {{$row->user_full_name}} - {{$row->created_at}}" style="cursor: pointer;">{{$row->created_at}}</span></td>
                </tr>
                @endforeach
                @endif

                
                @if(!$object_data->count())
                <tr>
                  <td colspan="{{$colspan}}" class="text-center text-danger">No matching records found</td>
                </tr>
                @endif
              </tbody></table>
              </div>
 <div class="col-md-12">          
              {{ $object_data->links() }}
            </div>
            <script>
             $(function ($) {
                $('.pagination>li a').addClass("pagination-link");


                
            });
          </script>      