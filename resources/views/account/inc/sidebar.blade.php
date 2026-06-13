<aside>

	<div class="inner-box">

		<div class="user-panel-sidebar">



			<div class="collapse-box">

				<ul class="acc-list">

					<li>

						<a href="{{ url(config('app.locale') . '/') }}">
							<i class="icon-home"></i>
							Home
						</a>

					</li>
				</ul>
				<h5 class="collapse-title no-border">

					{{ t('My Account') }}&nbsp;

					<a href="#MyClassified" data-toggle="collapse" class="pull-right"><i class="fa fa-angle-down"></i></a>

				</h5>

				<div class="panel-collapse collapse in" id="MyClassified">


					<ul class="acc-list">

						 


						<li>

							<a {!! ($pagePath=='') ? 'class="active"' : '' !!} href="{{ lurl('account') }}">

								<i class="icon-user"></i>

								{{ t('Personal Home') }}

							</a>

						</li>

                        <li>

							<a {!! ($pagePath=='recharge_points') ? 'class="active"' : '' !!} href="{{ lurl('account/recharge_points') }}">

								<i class="icon-money"></i>

								 {{ t('Balance') }}

							</a>

						</li>

                        
						<li>

							<a {!! ($pagePath=='transactions') ? ' class="active"' : '' !!} href="{{ lurl('account/transactions') }}">

							<i class="icon-ok"></i> {{ t('Transactions') }}&nbsp;

							<span class="badge">

								{{ isset($countTransactions) ? \App\Helpers\Number::short($countTransactions) : 0 }}

							</span>

							</a>

						</li>


                         <li>

                         

                          @if (isset($user->id) and !empty($user->id))

                                                

                                                    <?php $attr = ['countryCode' => config('country.icode'), 'id' => $user->id]; ?>

                                                    <a href="{{ lurl(trans('routes.v-search-user', $attr), $attr) }}">

                                                     <i class="icon-user fa hidden-sm"></i>  
													
													 
													 MyPage
													 

                                                    </a>

                                              

                         @else

                                                <h3 class="no-margin"><i class="icon-user fa hidden-sm"></i>{{ $user->username }} Home Page</h3>

                                                <!--<h3 class="no-margin">{{ $post->contact_name }}</h3>-->

                        @endif

                                            

                                            

                                            

							 

						</li>

                        

                        
						<li>

							<a  href="{{ lurl('generalSettings') }}">

								<i class="icon-user"></i>

								General Settings

							</a>

						</li>
						<li>

							<a {!! ($pagePath=='close') ? 'class="active"' : '' !!} href="{{ lurl('account/close') }}">

								<i class="icon-cancel-circled "></i> {{ t('Close account') }}

							</a>

						</li>

                                

                                

					</ul>

				</div>

			</div>

			<!-- /.collapse-box  -->



			<div class="collapse-box">

				<h5 class="collapse-title">

					{{ t('Ads') }}

					<a href="#MyAds" data-toggle="collapse" class="pull-right"><i class="fa fa-angle-down"></i></a>

				</h5>

				<div class="panel-collapse collapse in" id="MyAds">

					<ul class="acc-list">

						<li>

							<a{!! ($pagePath=='my-posts') ? ' class="active"' : '' !!} href="{{ lurl('account/my-posts') }}">

							<i class="icon-docs"></i> {{ t('My ads') }}&nbsp;

							<span class="badge">

								{{ isset($countMyPosts) ? \App\Helpers\Number::short(\App\Models\Post::where('user_id',auth()->user()->id)->count()) : 0 }}

							</span>

							</a>

						</li>

						

						<li style="display:none">

							<a{!! ($pagePath=='saved-search') ? ' class="active"' : '' !!} href="{{ lurl('account/saved-search') }}">

							<i class="icon-star-circled"></i> {{ t('Saved searches') }}&nbsp;

							<span class="badge">

								{{ isset($countSavedSearch) ? \App\Helpers\Number::short($countSavedSearch) : 0 }}

							</span>

							</a>

						</li>

						<li>

							<a{!! ($pagePath=='approved') ? ' class="active"' : '' !!} href="{{ lurl('account/approved') }}">

							<i class="icon-thumbs-up"></i> {{ t('Activated ads') }}&nbsp;

							<span class="badge">

								{{ isset($countApprovedPosts) ? \App\Helpers\Number::short($countApprovedPosts) : 0 }}

							</span>

							</a>

						</li>

						<li>

							<a{!! ($pagePath=='rejected') ? ' class="active"' : '' !!} href="{{ lurl('account/rejected') }}">

							<i class="icon-thumbs-down"></i> {{ t('Rejected ads') }}&nbsp;

							<span class="badge">

								{{ isset($countRejectedPosts) ? \App\Helpers\Number::short($countRejectedPosts) : 0 }}

							</span>

							</a>

						</li>

						<li>

							<a{!! ($pagePath=='pending-approval') ? ' class="active"' : '' !!} href="{{ lurl('account/pending-approval') }}">

							<i class="icon-hourglass"></i> {{ t('Pending approval') }}&nbsp;

							<span class="badge">

								{{ isset($countPendingPosts) ? \App\Helpers\Number::short($countPendingPosts) : 0 }}

							</span>

							</a>

						</li>

						<li>

							<a{!! ($pagePath=='archived') ? ' class="active"' : '' !!} href="{{ lurl('account/archived') }}">

							<i class="icon-folder-close"></i> {{ t('Archived ads') }}&nbsp;

							<span class="badge">

								{{ isset($countArchivedPosts) ? \App\Helpers\Number::short($countArchivedPosts) : 0 }}

							</span>

							</a>

						</li>

                        

                        <li>

							<a{!! ($pagePath=='favourite') ? ' class="active"' : '' !!} href="{{ lurl('account/favourite') }}">

							<i class="icon-heart"></i> {{ t('Favourite ads') }}

							<span class="badge">

								{{ isset($countFavouritePosts) ? \App\Helpers\Number::short($countFavouritePosts) : 0 }}

							</span>

							</a>

						</li>

                        

                         <li>

							<a{!! ($pagePath=='favourite-user') ? ' class="active"' : '' !!} href="{{ lurl('account/favourite-user') }}">

							<i class="icon-heart"></i> Favourite users

							<span class="badge">

								{{ isset($countFavouriteUsers) ? \App\Helpers\Number::short($countFavouriteUsers) : 0 }}

							</span>

							</a>

						</li>

                        

                        

						<li>

							<a{!! ($pagePath=='conversations') ? ' class="active"' : '' !!} href="{{ lurl('account/conversations') }}">

							<i class="icon-mail-1"></i> {{ t('Messages') }}

							<!--{{ t('Conversations') }}-->

							&nbsp;

							<span class="badge">

								{{ isset($countConversations) ? \App\Helpers\Number::short($countConversations) : 0 }}

							</span>&nbsp;

							</a>

						</li>

						



						<li>

							<a{!! ($pagePath=='makeanoffers') ? ' class="active"' : '' !!} href="{{ lurl('account/makeanoffers') }}">

							<i class="glyphicon glyphicon-hand-left"></i> {{ t('Offers') }}&nbsp;

							<span class="badge">

								{{ isset($countMakeanoffers) ? \App\Helpers\Number::short($countMakeanoffers) : 0 }}

							</span>

							</a>

						</li>



						@if (isset($apiPlugin) and !empty($apiPlugin))

							<li>

								<a{!! ($pagePath=='api-dashboard') ? ' class="active"' : '' !!} href="{{ lurl('account/api-dashboard') }}">

									<i class="icon-cog"></i> {{ trans('api::messages.Clients & Applications') }}&nbsp;

								</a>

							</li>

						@endif

					</ul>

				</div>

			</div>
 

		</div>

	</div>

	<!-- /.inner-box  -->

</aside>