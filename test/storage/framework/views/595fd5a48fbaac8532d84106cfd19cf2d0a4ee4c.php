<?php $notCount = '0'; ?>
<?php if(Session::get('notification_expire_date')): ?>
<?php $notCount++; ?>
<?php endif; ?>
              <?php if(Session::get('password_expire_date')): ?>
                   <?php $notCount++; ?>
              <?php endif; ?>

              <?php if(Auth::user()->user_role  == Session('user_role_super_admin')): ?>
                <?php if(Session::get('audit_delete_notification')): ?>
                <?php $notCount=$notCount+count(Session::get('audit_delete_notification'));?>
                <?php endif; ?>
              <?php endif; ?>
              <!-- count of assigned documents -->
              <?php if(Session::get('count_doc_assigned')): ?>
                <?php $notCount = $notCount+Session::get("count_doc_assigned");?>
              <?php endif; ?>
              <!-- count of rejected documents -->
              <?php if(Session::get('count_doc_rejected')): ?>
                <?php $notCount = $notCount+Session::get("count_doc_rejected");?>
              <?php endif; ?>
              <!-- count of accepted documents -->
              <?php if(Session::get('count_doc_accepted')): ?>
                <?php $notCount = $notCount+Session::get("count_doc_accepted");?>
              <?php endif; ?>

              
               <?php if(Session::get('total_notification_count')): ?>
                <?php $notCount = $notCount+Session::get("total_notification_count");?>
              <?php endif; ?>
<?php if($notCount): ?>
 <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                    <i class="fa fa-bell-o"></i>
                    <span class="label label-warning"><?php echo e(@$notCount); ?></span>
                  </a>

                  <ul class="dropdown-menu" style="height: auto;width: auto;">
                    <li class="header"> You have <?php echo e(@$notCount); ?> notifications
<a href="<?php echo e(url('notifications_list')); ?>" class="pull-right">
                          
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
                          <a href="<?php echo e(url('/listview?view=list&notification=1')); ?>"><span class="fa fa-star text-yellow"></span><b><?php echo e($count_expired_docs); ?></b> <?php if($count_expired_docs == 1): ?> document <?php else: ?> documents <?php endif; ?> will expire soon
                          </a>
                        </li>
                        <?php 
                        } 
                        ?>
                      <!-- new end -->
                        <!-- Audit delete notification-->
                        <?php if(Auth::user()->user_role  == Session('user_role_super_admin')): ?>
                          <?php 
                          if(Session::get('audit_delete_notification'))
                          {
                            $i=0;
                          foreach (Session::get('audit_delete_notification') as $value) {?>
                          <li>
                            <a href="javascript:viod(0)" id="<?php if(@Session::get('audits_delete_request_username')[$i]):echo 'delete-audits';else: echo 'delete-audits-request';endif;?>" user_full_name="<?php echo e(@Session::get('purge_audits_user_full_name')[$i]); ?>" user_name="<?php echo e(@Session::get('audits_delete_request_username')[$i]); ?>" delete_from_date="<?php echo e(@Session::get('delete_from_date')[$i]); ?>" delete_to_date="<?php echo e(@Session::get('delete_to_date')[$i]); ?>" request_username="<?php echo e(@Session::get('request_username')); ?>" ><span class="fa fa-star text-yellow"></span><?php echo @$value;?>
                            </a>
                          </li>
                          <?php $i++;?>
                          <?php } 
                          }?>
                        <?php endif; ?>

                         <!--Password expiry-->
                        <?php if(Session::get('password_expire_date')): ?>
                        <li>
                          <a href="<?php echo e(url('/userEdit')); ?>/<?php echo Auth::user()->id;?>"><span class="fa fa-star text-yellow"></span><?php echo Session::get('password_expire_date');?></a>
                        </li>
                        <?php endif; ?>
                        <!-- Document assign notofication -->
                        <?php if(Session::get('count_doc_assigned')): ?>
                        <li>
                          <a href="<?php echo e(url('/listview?view=list&notification=2')); ?>"><span class="fa fa-star text-yellow"></span><b><?php echo e(Session::get('count_doc_assigned')); ?></b> <?php if(Session::get('count_doc_assigned') == 1): ?> document is <?php else: ?> documents are <?php endif; ?> assigned for you 
                          </a>
                        </li>
                        <?php endif; ?>
                        <!-- document rejected notification -->
                        <?php if(Session::get('count_doc_rejected')): ?>
                        <li>
                          <a href="<?php echo e(url('/listview?view=list&notification=3')); ?>"><span class="fa fa-star text-yellow"></span><b><?php echo e(Session::get('count_doc_rejected')); ?></b> <?php if(Session::get('count_doc_rejected') == 1): ?> document <?php else: ?> documents  <?php endif; ?> has been rejected 
                          </a>
                        </li>
                        <?php endif; ?>
                        <!-- document accepted notification -->
                        <?php if(Session::get('count_doc_accepted')): ?>
                        <li>
                          <a href="<?php echo e(url('/listview?view=list&notification=4')); ?>"><span class="fa fa-star text-yellow"></span><b><?php echo e(Session::get('count_doc_accepted')); ?></b> <?php if(Session::get('count_doc_accepted') == 1): ?> document <?php else: ?> documents  <?php endif; ?> has been accepted successfully 
                          </a>
                        </li>
                        <?php endif; ?>
                        

                        <?php
                        $total_notification_list = (Session::get('total_notification_list'))?Session::get('total_notification_list'):array();
                        ?>
                        <?php $__currentLoopData = $total_notification_list; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $t_not): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                         <li>
                          <a href="<?php echo url($t_not->notification_link) ?>" class="read_notification" data-notid="<?php echo e($t_not->id); ?>"><span class="fa fa-star text-yellow"></span><?php echo e($t_not->notification_title); ?>

                          </a>
                        </li>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <li>
                          <a href="<?php echo e(url('notifications_list')); ?>">
                          <span class="fa fa-star text-yellow"></span>
                          View All
                          </a>
                        </li>
                      </ul>
                    </li>
                  </ul>
                  <?php else: ?>
                  <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                    <i class="fa fa-bell-o"></i>
                    <span class="label label-warning">0</span>
                  </a>

                  <ul class="dropdown-menu">
                    <li class="header">You have 0 notifications</li>
                    <li>
                      <a href="<?php echo e(url('notifications_list')); ?>">
                      <span class="fa fa-star text-yellow"></span>
                      View All
                      </a>
                    </li>
                  </ul>
<?php endif; ?><?php /**PATH F:\xampp\htdocs\nspl5.8\resources\views/pages/notification/top_notification.blade.php ENDPATH**/ ?>