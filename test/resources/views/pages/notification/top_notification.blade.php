@php $notCount = '0'; @endphp
@if(Session::get('notification_expire_date'))
@php $notCount++; @endphp
@endif
              @if(Session::get('password_expire_date'))
                   @php $notCount++; @endphp
              @endif

              @if(Auth::user()->user_role  == Session('user_role_super_admin'))
                @if(Session::get('audit_delete_notification'))
                <?php $notCount=$notCount+count(Session::get('audit_delete_notification'));?>
                @endif
              @endif
              <!-- count of assigned documents -->
              @if(Session::get('count_doc_assigned'))
                <?php $notCount = $notCount+Session::get("count_doc_assigned");?>
              @endif
              <!-- count of rejected documents -->
              @if(Session::get('count_doc_rejected'))
                <?php $notCount = $notCount+Session::get("count_doc_rejected");?>
              @endif
              <!-- count of accepted documents -->
              @if(Session::get('count_doc_accepted'))
                <?php $notCount = $notCount+Session::get("count_doc_accepted");?>
              @endif

              
               @if(Session::get('total_notification_count'))
                <?php $notCount = $notCount+Session::get("total_notification_count");?>
              @endif
@if($notCount)
 <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                    <i class="fa fa-bell-o"></i>
                    <span class="label label-warning">{{@$notCount}}</span>
                  </a>

                  <ul class="dropdown-menu" style="height: auto;width: auto;">
                    <li class="header"> You have {{@$notCount}} notifications
<a href="{{url('notifications_list')}}" class="pull-right">
                          
                          View All
                          </a>
                    </li>
                    <li>
                      <!-- inner menu: contains the actual data -->
                      <div class="slimScrollDiv" style="position: relative; overflow: hidden; width: auto; height: auto;">
                      <ul class="menu">
                      <!-- notification of expired docs with name and days old -->
                       
                      <!-- old end -->
                      <!-- new notification number of docs expired -->
                        <?php 
                        if(Session::get('notification_expire_date'))
                        {$count_expired_docs = count(Session::get('notification_expire_date'));
                        ?>
                        <li>
                          <a href="{{url('/listview?view=list&notification=1')}}"><span class="fa fa-star text-yellow"></span><b>{{$count_expired_docs}}</b> @if($count_expired_docs == 1) document @else documents @endif will expire soon
                          </a>
                        </li>
                        <?php 
                        } 
                        ?>
                      <!-- new end -->
                        <!-- Audit delete notification-->
                        @if(Auth::user()->user_role  == Session('user_role_super_admin'))
                          <?php 
                          if(Session::get('audit_delete_notification'))
                          {
                            $i=0;
                          foreach (Session::get('audit_delete_notification') as $value) {?>
                          <li>
                            <a href="javascript:viod(0)" id="<?php if(@Session::get('audits_delete_request_username')[$i]):echo 'delete-audits';else: echo 'delete-audits-request';endif;?>" user_full_name="{{@Session::get('purge_audits_user_full_name')[$i]}}" user_name="{{@Session::get('audits_delete_request_username')[$i]}}" delete_from_date="{{@Session::get('delete_from_date')[$i]}}" delete_to_date="{{@Session::get('delete_to_date')[$i]}}" request_username="{{@Session::get('request_username')}}" ><span class="fa fa-star text-yellow"></span><?php echo @$value;?>
                            </a>
                          </li>
                          <?php $i++;?>
                          <?php } 
                          }?>
                        @endif

                         <!--Password expiry-->
                        @if(Session::get('password_expire_date'))
                        <li>
                          <a href="{{url('/userEdit')}}/<?php echo Auth::user()->id;?>"><span class="fa fa-star text-yellow"></span><?php echo Session::get('password_expire_date');?></a>
                        </li>
                        @endif
                        <!-- Document assign notofication -->
                        @if(Session::get('count_doc_assigned'))
                        <li>
                          <a href="{{url('/listview?view=list&notification=2')}}"><span class="fa fa-star text-yellow"></span><b>{{Session::get('count_doc_assigned')}}</b> @if(Session::get('count_doc_assigned') == 1) document is @else documents are @endif assigned for you 
                          </a>
                        </li>
                        @endif
                        <!-- document rejected notification -->
                        @if(Session::get('count_doc_rejected'))
                        <li>
                          <a href="{{url('/listview?view=list&notification=3')}}"><span class="fa fa-star text-yellow"></span><b>{{Session::get('count_doc_rejected')}}</b> @if(Session::get('count_doc_rejected') == 1) document @else documents  @endif has been rejected 
                          </a>
                        </li>
                        @endif
                        <!-- document accepted notification -->
                        @if(Session::get('count_doc_accepted'))
                        <li>
                          <a href="{{url('/listview?view=list&notification=4')}}"><span class="fa fa-star text-yellow"></span><b>{{Session::get('count_doc_accepted')}}</b> @if(Session::get('count_doc_accepted') == 1) document @else documents  @endif has been accepted successfully 
                          </a>
                        </li>
                        @endif
                        

                        @php
                        $total_notification_list = (Session::get('total_notification_list'))?Session::get('total_notification_list'):array();
                        @endphp
                        @foreach($total_notification_list as $t_not)
                         <li>
                          <a href="@php echo url($t_not->notification_link) @endphp" class="read_notification" data-notid="{{$t_not->id}}"><span class="fa fa-star text-yellow"></span>{{$t_not->notification_title}}
                          </a>
                        </li>
                        @endforeach
                        <li>
                          <a href="{{url('notifications_list')}}">
                          <span class="fa fa-star text-yellow"></span>
                          View All
                          </a>
                        </li>
                      </ul>
                    </li>
                  </ul>
                  @else
                  <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                    <i class="fa fa-bell-o"></i>
                    <span class="label label-warning">0</span>
                  </a>

                  <ul class="dropdown-menu">
                    <li class="header">You have 0 notifications</li>
                    <li>
                      <a href="{{url('notifications_list')}}">
                      <span class="fa fa-star text-yellow"></span>
                      View All
                      </a>
                    </li>
                  </ul>
@endif