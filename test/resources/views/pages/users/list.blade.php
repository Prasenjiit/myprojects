<div class="row">
        <div class="col-xs-12">
            <?php
                include (public_path()."/storage/includes/lang1.en.php" );
                $user_permission=Auth::user()->user_permission;
            ?>
            <div class="box box-info">
                <div class="box-body">
                    <table border="1" id="example2" class="table table-bordered table-striped dataTable responsive hover" role="grid" aria-describedby="example1_info">
                        <thead>
                            <tr>
                                <th>{{trans('language.sign in name')}}</th>
                                <th>{{trans('language.full name')}}</th>
                                <th>{{trans('language.user type')}}</th>
                                <th>@if(Session::get('settings_department_name')) {{ Session::get('settings_department_name') }}@else {{trans('language.departments')}} @endif</th>
                                <th>{{trans('language.created date') }}</th>
                                <th nowrap="nowrap">{{ trans('language.last sign in') }}</th>
                                <th nowrap="nowrap">{{ trans('language.last_paswd_change') }}</th>
                                <th>{{ trans('language.status') }}</th>
                                <th>{{ trans('language.actions') }}</th>
                            </tr>
                        </thead>
                        <tfoot>
                            <tr>
                                <td><input type="text" id="loginname" placeholder="Search Sign In Name"></td>
                                <td><input type="text" id="fullname" placeholder="Search Full Name"></td>
                                <td><select name="usertype" id="usertype">
                                <option value="" selected="selected">All</option>   
                                <option value="{{trans('language.super admin')}}">{{trans('language.super admin')}}</option>   
                                <option value="{{trans('language.group admin')}}">{{trans('language.group admin')}}</option>   
                                <option value="{{trans('language.regular user')}}">{{trans('language.regular user')}}</option>   
                                </select></td>
                                <td><input type="text" id="depts" placeholder="Search Departments"></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                            </tr>
                        </tfoot>

                        <tbody>   
                            @foreach ($usersList as $key => $val)
                            <!-- According to the different users and permissions set variables-->
                            <?php $url=NULL; ?>
                            @if(Auth::user()->user_role == Session::get('user_role_group_admin') && $val->user_role == Session::get('user_role_group_admin') && Auth::user()->id != $val->id)
                            @else
                            @if((Auth::user()->user_role == Session::get('user_role_regular_user') && Auth::user()->id != $val->id) || (Auth::user()->user_role == Session::get('user_role_private_user') && Auth::user()->id != $val->id))
                                @else
                                    @if(stristr($user_permission,"edit"))
                                        <?php $url = url('userEdit', $val->id );?>
                                    @endif
                                @endif
                            @endif
                            <!-- //According to the different users and permissions set variables-->

                            <tr class="even" role="row">                                
                                <td>
                                @if(stristr($user_permission,'edit'))
                                    
                                    @if(Auth::user()->user_role == Session::get('user_role_super_admin')  || Auth::user()->user_role == Session::get('user_role_group_admin'))

                                        @if( (Auth::user()->user_role == Session::get('user_role_group_admin') && Auth::user()->id != $val->id) && ($val->user_role != Session::get('user_role_regular_user')) && ($val->user_role != Session::get('user_role_private_user')) ) 
                                            <a title="Edit" style="color:#23527c">
                                        @else
                                            <a title="Edit" href="{{ url('userEdit', $val->id ) }}">
                                        @endif

                                        @if(Auth::user()->id == $val->id) 
                                            <b>{{ $val->username }}</b>
                                        @else
                                        {{ $val->username }}
                                        @endif
                                       </a>
                                    @else
                                        @if(Auth::user()->id == $val->id)
                                            <a title="Edit" href="{{ url('userEdit', $val->id ) }}">
                                           <b>{{ $val->username }}</b>
                                        @else
                                            {{ $val->username }}
                                           </a>
                                        @endif
                                    @endif
                                
                                @else
                                    @if(Auth::user()->id == $val->id)
                                       <b>{{ $val->username }}</b>
                                    @else
                                    {{ $val->username }}
                                    @endif
                                @endif
                                </td>
                                <td>{{ $val->user_full_name }}</td>
                                <td>
                                    @if ($val->user_role == Session::get('user_role_super_admin'))
                                        <span class="label label-success"><b style="font-size: 12px;">{{ trans('language.super admin') }}</b></span>
                                    @elseif ($val->user_role == Session::get('user_role_group_admin'))
                                        <span class="label label-danger"><b style="font-size: 12px;">{{ trans('language.group admin') }}</b></span>          
                                    @elseif ($val->user_role == Session::get('user_role_regular_user'))
                                        <span class="label label-primary"><b style="font-size: 12px;">{{ trans('language.regular user') }}</b></span> @if ($val->user_view_only == '1') <span class="label btn-success"><b style="font-size: 12px;">{{ trans('language.viewonly') }} </b>@endif </span>
                                    @elseif ($val->user_role == Session::get('user_role_private_user'))
                                        <span class="label label-warning"><b style="font-size: 12px;">{{ trans('language.private_user') }}</b></span>
                                    @endif
                                </td>
                               
                                <td><!-- Department -->
                                {{ $val->departments[0]->department_name }}
                                </td>
                                <?php /*<td><?php echo date('d-m-Y H:i:s a', strtotime($val->created_at)); ?></td>
                                <td><?php echo date('d-m-Y H:i:s a', strtotime($val->user_lastlogin_date)); ?></td>*/?>
                                <td nowrap="nowrap"><?php echo dtFormat($val->created_at); ?></td>
                                <td nowrap="nowrap"><?php echo dtFormat($val->user_lastlogin_date); ?></td>
                                <!-- Password Date -->
                                <td><?php if($val->password_date=="0000-00-00"){ echo"-"; }else{ echo dtFormat($val->password_date); }?></td>
                                <td style="text-align:center;">
                                <?php 
                                    //checking the password date is not 0 
                                    if($val->password_date!="0000-00-00"){
                                        $todate = date('Y-m-d');
                                        $paswdDate = $val->password_date;
                                        $startTimeStamp = strtotime($todate);
                                        $endTimeStamp = strtotime($paswdDate);

                                        $timeDiff = abs($endTimeStamp - $startTimeStamp);
                                        $numberDays = $timeDiff/86400;  // 86400 seconds in one day
                                        
                                        // and you might want to convert to integer
                                        $numberDays = intval($numberDays);
                                        ?>
                                        <!-- cheking user is lockedout -->
                                        @if ($val->user_lock_status == 1)
                                            <!-- cheking user role  -->
                                            @if((Auth::user()->user_role == Session::get('user_role_super_admin') )&&($val->id!=Auth::user()->user_role))
                                                <a title="{{ trans('language.locl_title') }}" href="javascript:void(0);" onclick="unlock({{ $val->id}},'{{$val->username }}')">
                                                    <li class="fa fa-lock fa-lg" ></li>
                                                </a>
                                            <!-- cheking user is superadmin -->
                                            @else
                                                <a title="{{ trans('language.locl_title') }}" style="cursor:default">
                                                    <li class="fa fa-lock fa-lg" ></li>
                                                </a>
                                            @endif
                                            <!-- cheking user is not lockedout -->
                                        @else    
                                        	<!-- checking password is expired or not -->
                                        	@if($numberDays>$expry_no)
                                                <a title="{{ trans('language.passwd_expired') }}" style="cursor:default">
                                                    <li class="fa fa-key fa-lg" ></li>
                                                </a>
                                            @else
                                            	@if ($val->login_status == Session::get('login_status_Login'))
                                                    @if((Auth::user()->user_role == Session::get('user_role_super_admin') )&&($val->id!=Auth::user()->user_role))
                                                        <a title="{{ trans('language.signout_title') }}" href="javascript:void(0);" onclick="logout({{ $val->id}},'{{$val->username }}')">
                                                            <li class="fa fa-power-off fa-lg" ></li>
                                                        </a>
                                                    @else
                                                        <a title="{{ trans('language.signout_title') }}" style="cursor:default">
                                                            <li class="fa fa-power-off fa-lg" ></li>
                                                        </a>
                                                    @endif
                                                @else

                                                @if ($val->user_status == Session::get('user_status_Inactive'))
                                                    <a @if($url) title="{{ trans('language.inactive_title') }}" href="<?php echo $url;?>" @else title="{{ trans('language.currently_inactive_title') }}"  @endif>
                                                        <span class="label label-danger"><b style="font-size: 12px;">{{ trans('language.inactive') }}</b></span>
                                                    </a>
                                                @else 
                                                    <a  @if($url) title="{{ trans('language.active_title') }}" href="<?php echo $url;?>" @else title="{{ trans('language.currently_active_title') }}" @endif>
                                                        <span class="label label-success"><b style="font-size: 12px;">{{trans('language.active')}}</b></span>
                                                    </a>                                                        
                                                @endif

                                                @endif
                                            @endif
                                        @endif
                                        <?php
                                    }
                                ?>
                                        
                                </td>

                                <td width="5%">
                                    @if((Auth::user()->user_role == Session::get('user_role_super_admin') ) or (Auth::user()->user_role == Session::get('user_role_group_admin')))
                                        <a title="History" href="{{ url('userHistory', $val->username ) }}">
                                            <li class="fa fa-history" ></li>
                                        </a>
                                    @endif
                                    
                                    @if(Auth::user()->user_role == Session::get('user_role_group_admin') && $val->user_role == Session::get('user_role_group_admin') && Auth::user()->id != $val->id)
                                    @else
                                    @if((Auth::user()->user_role == Session::get('user_role_regular_user') && Auth::user()->id != $val->id) || (Auth::user()->user_role == Session::get('user_role_private_user') && Auth::user()->id != $val->id))
                                        @else
                                            @if(stristr($user_permission,"edit"))
                                                &nbsp;
                                                <a title="Edit" href="{{ url('userEdit', $val->id ) }}">
                                                    <li class="fa fa-pencil" ></li>
                                                </a>
                                            @endif
                                            
                                            @if(stristr($user_permission,"delete"))
                                                @if(Auth::user()->id != $val->id && $val->user_role != Session::get('user_role_super_admin'))
                                                    &nbsp;
                                                    <li title="Delete" class="fa fa-trash" onclick="del({{ $val->id }}, '{{ $val->username }}')" style="color: red; cursor:pointer;"></li>
                                                @endif
                                            @endif
                                        @endif
                                    @endif
                                </td>
                            </tr>
                            @endforeach                     

                        </tbody>                    
                    </table>
                </div><!-- /.box-body -->
            </div><!-- /.box -->
        </div><!-- /.col -->
    </div><!-- /.row -->
    {!! Html::script('plugins/jQuery/jQuery-2.1.4.min.js') !!}
    {!! Html::script('plugins/datatables_new/jquery.dataTables.min.js') !!}

<script>
    $(function () {
        
        var rows_per_page = '{{Session('settings_rows_per_page')}}';
        var lengthMenu = getLengthMenu();//Function in app.blade.php
        var table = $('#example2').DataTable({
            "paging": true,
            "lengthMenu": lengthMenu,
            "searching": true,
            "ordering": true,
            //"info": true,
            //"autoWidth": false,
            "pageLength":rows_per_page,
            "order": [[ 0, "asc" ]],
            "scrollX": true,
            "scrollY":350,
            language: {
                searchPlaceholder: "{{ trans('language.data_tbl_search_placeholder') }}"
            },
            columnDefs: [ { orderable: false, targets: [7,8] } ]
        });
        //for highlight the row
    $('#example2 tbody').on('click', 'tr', function () {
        $("#example2 tbody tr").removeClass('row_selected');        
        $(this).addClass('row_selected');
    });
		$('#loginname').on('keyup', function(){
            table
            .column(0)
            .search(this.value)
            .draw();
        });  
		
		$('#fullname').on('keyup', function(){
            table
            .column(1)
            .search(this.value)
            .draw();
        }); 
		
		$('#usertype').on('change', function(){
            table
            .column(2)
            .search(this.value)
            .draw();
        }); 
		
		$('#depts').on('keyup', function(){
            table
            .column(3)
            .search(this.value)
            .draw();
        }); 


    });

  
    function logout(id,username)
    {   

        swal({
              title: "{{trans('language.confirm_logout')}}'" + username + "' ?",
              text: "{{trans('language.Swal_not_revert')}}",
              type: "{{trans('language.Swal_warning')}}",
              showCancelButton: true
            }).then(function (result) {
                if(result){
                    // Success
                    var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
                    $.ajax({
                        type: 'post',
                        url: '{{URL('userLogout')}}',
                        dataType: 'html',
                        data: {_token: CSRF_TOKEN, id:id,name:username},
                        timeout: 50000,
                        beforeSend: function() {
                            $("#bs").show();
                        },
                        success: function(data, status){ 
                            setTimeout(function () {
                                $("#msg").html('<div class="alert alert-success alert-sty">'+ data +'</div>');
                                $("#msg").slideDown(1000);
                            }, 200);
                            setTimeout(function () {
                                $('#msg').slideUp("slow");
                                window.location.reload();
                            }, 5000);

                        },
                        error: function(jqXHR, textStatus, errorThrown){
                            console.log(jqXHR);    
                            console.log(textStatus);    
                            console.log(errorThrown);    
                        },
                        complete: function() {
                           $("#bs").hide();
                        }
                    });      
              }
          });
    }


    function unlock(id,username)
    {   

         swal({
              title: "{{trans('language.confirm_unlock')}}'" + username + "' ?",
              text: "{{trans('language.Swal_not_revert')}}",
              type: "{{trans('language.Swal_warning')}}",
              showCancelButton: true
            }).then(function (result) {
                if(result){
                    // Success
                    var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
                    $.ajax({
                        type: 'post',
                        url: '{{URL('userUnlock')}}',
                        dataType: 'html',
                        data: {_token: CSRF_TOKEN, id:id,name:username},
                        timeout: 50000,
                        beforeSend: function() {
                            $("#bs").show();
                        },
                        success: function(data, status){ 
                            setTimeout(function () {
                                $("#msg").html('<div class="alert alert-success alert-sty">'+ data +'</div>');
                                $("#msg").slideDown(1000);
                            }, 200);
                            setTimeout(function () {
                                $('#msg').slideUp("slow");
                                window.location.reload();
                            }, 5000);

                        },
                        error: function(jqXHR, textStatus, errorThrown){
                            console.log(jqXHR);    
                            console.log(textStatus);    
                            console.log(errorThrown);    
                        },
                        complete: function() {
                           $("#bs").hide();
                        }
                    }); 

              }
          });
    }
    
</script>  