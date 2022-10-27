<aside>
	<div class="inner-box">
		<div class="user-panel-sidebar">

      @if (isset($user))
      <div class="collapse-box">
        <h5 class="collapse-title no-border">
          {{ t('My Account') }}&nbsp;
          <a href="#MyClassified" data-toggle="collapse" class="pull-right"><i class="fa fa-angle-down"></i></a>
        </h5>
        <div class="panel-collapse collapse show" id="MyClassified">
          <ul class="acc-list">
            <li>
              <a {!! ($pagePath=='') ? 'class="active"' : '' !!} href="{{ lurl('account') }}">
                <i class="icon-home"></i> {{ t('Personal Home') }}
              </a>
            </li>
            
          
          </ul>
        </div>
      </div>
      @if (!empty($user->user_type_id) and $user->user_type_id == 2)
      <!-- social profile  -->
      <div class="collapse-box">
        <h5 class="collapse-title no-border">
          Social Profile&nbsp;
          <a href="#MySocial" data-toggle="collapse" class="pull-right"><i class="fa fa-angle-down"></i></a>
        </h5>
        <div class="panel-collapse collapse" id="MySocial">
          <ul class="acc-list">
            <li>
              <a {!! ($pagePath=='') ? 'class="active"' : '' !!} href="<?= '/account/socialprofile/edit'; ?>">
                <i class="icon-home"></i> {{ t('My Profile') }}
              </a>
            </li>
             <li>
              <a {!! ($pagePath=='') ? 'class="active"' : '' !!} href="<?= '/account/socialprofile/new'; ?>">
                <i class="icon-home"></i> {{ t('My Profile')  }} New
              </a>
            </li>
            <li>
              
              <a <?php if($_SERVER['REQUEST_URI'] == '/account/socialprofile/myrate'):?><?php endif;?>href="<?= '/account/socialprofile/myrate'; ?>">
              <i class="icon-price"></i>My Rate
            </a>
          </li>
            <li>
              
              <a <?php if($_SERVER['REQUEST_URI'] == '/account/socialprofile/portfolio'):?><?php endif;?>href="<?= '/account/socialprofile/portfolio'; ?>">
              <i class="icon-portfolio"></i>Portfolios
            </a>
          </li>


              <li>

                  <a <?php if($_SERVER['REQUEST_URI'] == '/account/socialprofile/instagram'):?><?php endif;?>href="<?= '/account/socialprofile/instagram'; ?>">
                      <i class="icon-portfolio"></i>Instagram
                  </a>
              </li>
        </ul>
      </div>
    </div>
    @endif
    <!-- /.collapse-box  -->

    @if (!empty($user->user_type_id) and $user->user_type_id != 0)
    <div class="collapse-box">
      <h5 class="collapse-title">
        {{ t('My Ads') }}&nbsp;
        <a href="#MyAds" data-toggle="collapse" class="pull-right"><i class="fa fa-angle-down"></i></a>
      </h5>
      <div class="panel-collapse collapse show" id="MyAds">
        <ul class="acc-list">
          <!-- COMPANY -->
          @if (in_array($user->user_type_id, [1]))
          <li>
            <a{!! ($pagePath=='companies') ? ' class="active"' : '' !!} href="{{ lurl('account/companies') }}">
            <i class="icon-town-hall"></i> {{ t('My companies') }}&nbsp;
            <span class="badge badge-pill">
             {{ isset($countCompanies) ? \App\Helpers\Number::short($countCompanies) : 0 }}
           </span>
         </a>
       </li>
       <li>
        <a{!! ($pagePath=='my-posts') ? ' class="active"' : '' !!} href="{{ lurl('account/my-posts') }}">
        <i class="icon-docs"></i> {{ t('My ads') }}&nbsp;
        <span class="badge badge-pill">
         {{ isset($countMyPosts) ? \App\Helpers\Number::short($countMyPosts) : 0 }}
       </span>
     </a>
   </li>
   <li>
    <a{!! ($pagePath=='pending-approval') ? ' class="active"' : '' !!} href="{{ lurl('account/pending-approval') }}">
    <i class="icon-hourglass"></i> {{ t('Pending approval') }}&nbsp;
    <span class="badge badge-pill">
     {{ isset($countPendingPosts) ? \App\Helpers\Number::short($countPendingPosts) : 0 }}
   </span>
 </a>
</li>
<li>
  <a{!! ($pagePath=='archived') ? ' class="active"' : '' !!} href="{{ lurl('account/archived') }}">
  <i class="icon-folder-close"></i> {{ t('Archived ads') }}&nbsp;
  <span class="badge badge-pill">
   {{ isset($countArchivedPosts) ? \App\Helpers\Number::short($countArchivedPosts) : 0 }}
 </span>
</a>
</li>
<li>
  <a{!! ($pagePath=='conversations') ? ' class="active"' : '' !!} href="{{ lurl('account/conversations') }}">
  <i class="icon-mail-1"></i> Applicants&nbsp;
  <span class="badge badge-pill">
    {{ isset($countConversations) ? \App\Helpers\Number::short($countConversations) : 0 }}
  </span>&nbsp;
  <span class="badge badge-pill badge-important count-conversations-with-new-messages">0</span>
</a>
</li>



<!-- USER WALLET -->
<li>
 <a <?php if($_SERVER['REQUEST_URI'] == '/account/wallet'):?><?= 'class="active"';?><?php endif;?> href="{{ lurl('account/wallet') }}">
   <i class="icon-money"></i>Wallet
 </a>
</li>
<!-- USER WALLET -->

<!-- Transactions -->
<li>
 <a <?php if($_SERVER['REQUEST_URI'] == '/account/transaction'):?><?= 'class="active"';?><?php endif;?> href="{{ lurl('account/transaction') }}">
   <i class='far fa-address-card'></i> Transactions
 </a>
</li>
<!-- Transactions -->


<!-- Recieved PROJECTS -->
<li>
  <a href="/account/recievedprojects">
    <i class="icon-mail-1"></i> Jobs & Project&nbsp;
    <span class="badge badge-pill">
     0
   </span>&nbsp;
   <span class="badge badge-important count-conversations-with-new-messages">0</span>
 </a>
</li>

<!-- Recieved PROJECTS -->
<!-- Withdraw Request -->
<li>
  <a href="/account/withdrawrequest">
    <i class='fas fa-book'></i> Withdraw Request
  </a>
</li>
<!-- Withdraw Request -->
<!-- <li>
    <a{!! ($pagePath=='transactions') ? ' class="active"' : '' !!} href="{{ lurl('account/transactions') }}">
    <i class="icon-money"></i> {{ t('Transactions') }}&nbsp;
    <span class="badge badge-pill">
       {{ isset($countTransactions) ? \App\Helpers\Number::short($countTransactions) : 0 }}
   </span>
</a>
</li> -->
@endif
<!-- CANDIDATE -->
@if (in_array($user->user_type_id, [2]))
<li>
  <a{!! ($pagePath=='resumes') ? ' class="active"' : '' !!} href="{{ lurl('account/resumes') }}">
  <i class="icon-attach"></i> {{ t('My resumes') }}&nbsp;
  <span class="badge badge-pill">
   {{ isset($countResumes) ? \App\Helpers\Number::short($countResumes) : 0 }}
 </span>
</a>
</li>
<li>
  <a{!! ($pagePath=='favourite') ? ' class="active"' : '' !!} href="{{ lurl('account/favourite') }}">
  <i class="icon-heart"></i> {{ t('Favourite jobs') }}&nbsp;
  <span class="badge badge-pill">
   {{ isset($countFavouritePosts) ? \App\Helpers\Number::short($countFavouritePosts) : 0 }}
 </span>
</a>
</li>
<li>
  <a{!! ($pagePath=='saved-search') ? ' class="active"' : '' !!} href="{{ lurl('account/saved-search') }}">
  <i class="icon-star-circled"></i> {{ t('Saved searches') }}&nbsp;
  <span class="badge badge-pill">
   {{ isset($countSavedSearch) ? \App\Helpers\Number::short($countSavedSearch) : 0 }}
 </span>
</a>
</li>
<li>
  <a{!! ($pagePath=='conversations') ? ' class="active"' : '' !!} href="{{ lurl('account/conversations') }}">
  <i class="icon-mail-1"></i> {{ t('Conversations') }}&nbsp;
  <span class="badge badge-pill">
   {{ isset($countConversations) ? \App\Helpers\Number::short($countConversations) : 0 }}
 </span>&nbsp;
 <span class="badge badge-important count-conversations-with-new-messages">0</span>
</a>
</li>
<!-- USER WALLET -->
<li>
 <a <?php if($_SERVER['REQUEST_URI'] == '/account/wallet'):?><?= 'class="active"';?><?php endif;?> href="{{ lurl('account/wallet') }}">
   <i class="icon-money"></i>Wallet
 </a>
</li>
<!-- USER WALLET -->
<!-- Transactions -->
<li>
 <a <?php if($_SERVER['REQUEST_URI'] == '/account/transaction'):?><?= 'class="active"';?><?php endif;?> href="{{ lurl('account/transaction') }}">
  <i class='far fa-address-card'></i> Transactions
</a>
</li>
<!-- Transactions -->
<!-- Recieved PROJECTS -->
<li>
  <a href="/account/recievedprojects">
    <i class="icon-mail-1"></i> Received Project &nbsp;
   <!--  <span class="badge badge-pill">
     {{ isset($recieve_projects_count) ? \App\Helpers\Number::short($recieve_projects_count) : 0 }}
   </span>&nbsp; -->
   <span class="badge badge-important count-conversations-with-new-messages">0</span>
 </a>
</li>

<!-- Recieved PROJECTS -->

<!-- AWARDED PROJECTS -->
<li>
  <a href="/account/awardedprojects">
    <i class="icon-mail-1"></i> Projects Awarded&nbsp;
  <!--   <span class="badge badge-pill">
     {{ isset($award_projects_count) ? \App\Helpers\Number::short($award_projects_count) : 0 }}
   </span>&nbsp; -->
   <span class="badge badge-important count-conversations-with-new-messages">0</span>
 </a>
</li>

<!-- AWARDED PROJECTS -->


<!-- Withdraw Request -->
<li>
  <a href="/account/withdrawrequest">
    <i class='fas fa-book'></i> Withdraw Request
  </a>
</li>
<!-- Withdraw Request -->

@endif
@if(config('plugins.apijc.installed'))
<li>
  <a{!! ($pagePath=='api-dashboard') ? ' class="active"' : '' !!} href="{{ lurl('account/api-dashboard') }}">
  <i class="icon-cog"></i> {{ trans('api::messages.Clients & Applications') }}&nbsp;
</a>
</li>
@endif
</ul>
</div>
</div>
<!-- /.collapse-box  -->

<div class="collapse-box">
  <h5 class="collapse-title">
    {{ t('Terminate Account') }}&nbsp;
    <a href="#TerminateAccount" data-toggle="collapse" class="pull-right"><i class="fa fa-angle-down"></i></a>
  </h5>
  <div class="panel-collapse collapse show" id="TerminateAccount">
    <ul class="acc-list">
      <li>
        <a {!! ($pagePath=='close') ? 'class="active"' : '' !!} href="{{ lurl('account/close') }}">
          <i class="icon-cancel-circled "></i> {{ t('Close account') }}
        </a>
      </li>
    </ul>
  </div>
</div>
<!-- /.collapse-box  -->
@endif
@endif

</div>
</div>
<!-- /.inner-box  -->
</aside>