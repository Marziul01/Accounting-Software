<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
    <div class="app-brand demo">
        <a href="{{ route('admin.dashboard') }}" class="app-brand-link justify-content-center">
            <img src="{{ asset($setting->site_logo) }}" alt="Logo" class="app-brand-logo demo" height="100%" width="40%" />
        </a>

        <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto d-block d-xl-none">
            <i class="bx bx-chevron-left bx-sm d-flex align-items-center justify-content-center"></i>
        </a>
    </div>

    <div class="menu-inner-shadow"></div>

    <ul class="menu-inner py-1">
        <!-- Dashboards -->

        <li class="menu-item {{ Route::currentRouteName() == 'admin.dashboard' ? 'active' : '' }}">
            <a href="{{ route('admin.dashboard') }}"
                class="menu-link">
                <i class="menu-icon tf-icons bx bx-home-smile"></i>
                <div class="text-truncate" data-i18n="Email">Dashboard</div>
            </a>
        </li>

        @if(Auth::user()->access->admin_panel == '1' || Auth::user()->access->admin_panel == '2')
        <li class="menu-item  {{ Route::currentRouteName() == 'admin.users' ? 'active open' : '' }} {{ Route::currentRouteName() == 'admin.categoryTableSettings' ? 'active open' : '' }} {{ Route::currentRouteName() == 'home.settings' ? 'active open' : '' }}  {{ Route::currentRouteName() == 'Export-Data' ? 'active open' : '' }}">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                {{-- <i class="menu-icon tf-icons bx bx-home-smile"></i> --}}
                <i class="menu-icon1 tf-icons fa-solid fa-user-gear"></i>
                <div class="text-truncate" data-i18n="Dashboards">Admin Panel</div>
            </a>
            <ul class="menu-sub">
                <li class="menu-item {{ Route::currentRouteName() == 'admin.users' ? 'active' : '' }}">
                    <a href="{{ route('admin.users') }}" class="menu-link">
                        <div class="text-truncate" data-i18n="Analytics">Control Panel</div>
                    </a>
                </li>

                <li class="menu-item {{ Route::currentRouteName() == 'admin.categoryTableSettings' ? 'active' : '' }}">
                    <a href="{{ route('admin.categoryTableSettings') }}" class="menu-link">
                        <div class="text-truncate" data-i18n="Analytics">Category & Table </div>
                    </a>
                </li>

                <li class="menu-item {{ Route::currentRouteName() == 'home.settings' ? 'active' : '' }}">
                    <a href="{{ route('home.settings') }}"
                        class="menu-link ">
                        <div class="text-truncate" data-i18n="CRM"> Landing Page Setting </div>
                    </a>
                </li>
                
                <li class="menu-item {{ Route::currentRouteName() == 'Export-Data' ? 'active' : '' }}">
                    <a href="{{ route('Export-Data') }}"
                        class="menu-link">
                        <div class="text-truncate" data-i18n="eCommerce">Export Data</div>
                    </a>
                </li>
            </ul>
        </li>
        @endif

        @if(Auth::user()->access->sms_and_email == '1' || Auth::user()->access->sms_and_email == '2')
        <li class="menu-item  {{ Route::currentRouteName() == 'occassion' ? 'active open' : '' }} {{ Route::currentRouteName() == 'emailsmsTemplate' ? 'active open' : '' }} ">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                {{-- <i class="menu-icon tf-icons bx bx-home-smile"></i> --}}
                <i class="menu-icon1 tf-icons fa-solid fa-message"></i>
                <div class="text-truncate" data-i18n="Dashboards">SMS and Email</div>
            </a>
            <ul class="menu-sub">
                <li class="menu-item {{ Route::currentRouteName() == 'sendSMSEmail' ? 'active' : '' }}">
                    <a href="{{ route('sendSMSEmail') }}" class="menu-link">
                        <div class="text-truncate" data-i18n="Analytics"> Send SMS and Email</div>
                    </a>
                </li>
                <li class="menu-item {{ Route::currentRouteName() == 'occassion' ? 'active' : '' }}">
                    <a href="{{ route('occassion') }}" class="menu-link">
                        <div class="text-truncate" data-i18n="Analytics"> Occasional SMS and Email</div>
                    </a>
                </li>
                <li class="menu-item {{ Route::currentRouteName() == 'emailsmsTemplate' ? 'active' : '' }}">
                    <a href="{{ route('emailsmsTemplate') }}"
                        class="menu-link">
                        <div class="text-truncate" data-i18n="CRM"> SMS and Email Template </div>
                    </a>
                </li>
                
            </ul>
        </li>
        @endif

        @if(Auth::user()->access->contact == '1' || Auth::user()->access->contact == '2')
        <li class="menu-item {{ Route::currentRouteName() == 'contact.index' ? 'active' : '' }}">
            <a href="{{ route('contact.index') }}"
                class="menu-link">
                
                <i class="menu-icon1 tf-icons fa-solid fa-address-book"></i>
                <div class="text-truncate" data-i18n="Email">Contact </div>
            </a>
        </li>
        @endif


        @if(Auth::user()->access->investment == '1' || Auth::user()->access->investment == '2')
        <li class="menu-item  {{ Route::currentRouteName() == 'investment.index' || Route::currentRouteName() == 'investmentcategory.index' || Route::currentRouteName() == 'investmentsubcategory.index' ? 'active open' : '' }} {{ Route::currentRouteName() == 'investment.report' ? 'active open' : '' }}  ">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon1 tf-icons fa-solid fa-money-bill-trend-up"></i>
                <div class="text-truncate" data-i18n="Dashboards">Investments Management</div>
            </a>
            <ul class="menu-sub">
                @if($categorysettings->investments_category == 2)
                <li class="menu-item {{ Route::currentRouteName() == 'investmentcategory.index' ? 'active' : '' }}">
                    <a href="{{ route('investmentcategory.index') }}" class="menu-link">
                        <div class="text-truncate" data-i18n="Analytics">Investments Category</div>
                    </a>
                </li>
                @endif
                <li class="menu-item {{ Route::currentRouteName() == 'investmentsubcategory.index' ? 'active' : '' }}">
                    <a href="{{ route('investmentsubcategory.index') }}" class="menu-link">
                        <div class="text-truncate" data-i18n="CRM">Investments {{ $categorysettings->investments_category == 2 ? 'Sub' : '' }} Category</div>
                    </a>
                </li>
                <li class="menu-item {{ Route::currentRouteName() == 'investment.index' ? 'active' : '' }}">
                    <a href="{{ route('investment.index') }}" class="menu-link">
                        <div class="text-truncate" data-i18n="eCommerce">Investments</div>
                    </a>
                </li>
                <li class="menu-item {{ Route::currentRouteName() == 'investment.report' ? 'active' : '' }}">
                    <a href="{{ route('investment.report') }}"
                        class="menu-link">
                        <div class="text-truncate" data-i18n="eCommerce">Reports</div>
                        
                    </a>
                </li>
            </ul>
        </li>
        @endif

        @if(Auth::user()->access->income == '1' || Auth::user()->access->income == '2')
        <li class="menu-item  {{ Route::currentRouteName() == 'income.index' || Route::currentRouteName() == 'incomecategory.index' || Route::currentRouteName() == 'incomesubcategory.index' ? 'active open' : '' }}  {{ Route::currentRouteName() == 'income.report' ? 'active open' : '' }}">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                
                <i class="menu-icon1 tf-icons fa-solid fa-circle-dollar-to-slot"></i>
                <div class="text-truncate" data-i18n="Dashboards">Incomes Managment</div>
            </a>
            <ul class="menu-sub">
                @if($categorysettings->income_category == 2)
                <li class="menu-item {{ Route::currentRouteName() == 'incomecategory.index' ? 'active' : '' }}">
                    <a href="{{ route('incomecategory.index') }}" class="menu-link">
                        <div class="text-truncate" data-i18n="Analytics">Incomes Category</div>
                    </a>
                </li>
                @endif
                <li class="menu-item {{ Route::currentRouteName() == 'incomesubcategory.index' ? 'active' : '' }}">
                    <a href="{{ route('incomesubcategory.index') }}"
                        class="menu-link">
                        <div class="text-truncate" data-i18n="CRM">Incomes {{ $categorysettings->income_category == 2 ? 'Sub' : '' }}  Category</div>
                    </a>
                </li>
                <li class="menu-item {{ Route::currentRouteName() == 'income.index' ? 'active' : '' }}">
                    <a href="{{ route('income.index') }}"
                        class="menu-link">
                        <div class="text-truncate" data-i18n="eCommerce">Incomes</div>
                        
                    </a>
                </li>
                <li class="menu-item {{ Route::currentRouteName() == 'income.report' ? 'active' : '' }}">
                    <a href="{{ route('income.report') }}"
                        class="menu-link">
                        <div class="text-truncate" data-i18n="eCommerce">Reports</div>
                        
                    </a>
                </li>
            </ul>
        </li>
        @endif

        @if(Auth::user()->access->expense == '1' || Auth::user()->access->expense == '2')
        <li class="menu-item  {{ Route::currentRouteName() == 'expense.index' || Route::currentRouteName() == 'expensecategory.index' || Route::currentRouteName() == 'expensesubcategory.index' ? 'active open' : '' }} {{ Route::currentRouteName() == 'expense.report' ? 'active open' : '' }} ">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                
                <i class="menu-icon1 tf-icons fa-solid fa-money-bill-transfer"></i>
                <div class="text-truncate" data-i18n="Dashboards">Expenses Managment</div>
            </a>
            <ul class="menu-sub">
                @if($categorysettings->expense_category == 2)
                <li class="menu-item {{ Route::currentRouteName() == 'expensecategory.index' ? 'active' : '' }}">
                    <a href="{{ route('expensecategory.index') }}" class="menu-link">
                        <div class="text-truncate" data-i18n="Analytics">Expenses Category</div>
                    </a>
                </li>
                @endif
                <li class="menu-item {{ Route::currentRouteName() == 'expensesubcategory.index' ? 'active' : '' }}">
                    <a href="{{ route('expensesubcategory.index') }}"
                         class="menu-link">
                        <div class="text-truncate" data-i18n="CRM">Expenses {{ $categorysettings->expense_category == 2 ? 'Sub' : '' }} Category</div>
                    </a>
                </li>
                <li class="menu-item {{ Route::currentRouteName() == 'expense.index' ? 'active' : '' }}">
                    <a href="{{ route('expense.index') }}"
                        class="menu-link">
                        <div class="text-truncate" data-i18n="eCommerce">Expenses</div>
                        
                    </a>
                </li>
                <li class="menu-item {{ Route::currentRouteName() == 'expense.report' ? 'active' : '' }}">
                    <a href="{{ route('expense.report') }}"
                        class="menu-link">
                        <div class="text-truncate" data-i18n="eCommerce">Reports</div>
                        
                    </a>
                </li>
            </ul>
        </li>
        @endif

        

        @if(Auth::user()->access->asset == '1' || Auth::user()->access->asset == '2')
        <li class="menu-item  {{ Route::currentRouteName() == 'asset.index' || Route::currentRouteName() == 'assetFixed' || Route::currentRouteName() == 'assetcategory.index' || Route::currentRouteName() == 'assetsubcategory.index' || Route::currentRouteName() == 'assetsubsubcategory.index' ? 'active open' : '' }} {{ Route::currentRouteName() == 'assets.report' ? 'active open' : '' }} ">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                
                <i class="menu-icon1 tf-icons fa-solid fa-hand-holding-dollar"></i>
                <div class="text-truncate" data-i18n="Dashboards">Assets Management</div>
            </a>
            <ul class="menu-sub">
                @if($categorysettings->asset_category == 2)
                <li class="menu-item {{ Route::currentRouteName() == 'assetcategory.index' ? 'active' : '' }}">
                    <a href="{{ route('assetcategory.index') }}" class="menu-link">
                        <div class="text-truncate" data-i18n="Analytics">Assets Category</div>
                    </a>
                </li>
                @endif
                
                <li class="menu-item {{ Route::currentRouteName() == 'assetsubcategory.index' ? 'active' : '' }}">
                    <a href="{{ route('assetsubcategory.index') }}" class="menu-link">
                        <div class="text-truncate" data-i18n="CRM">Assets {{ $categorysettings->asset_category == 2 ? 'Sub' : '' }} Category</div>
                    </a>
                </li>
                
                {{-- <li class="menu-item {{ Route::currentRouteName() == 'assetsubsubcategory.index' ? 'active' : '' }}">
                    <a href="{{ route('assetsubsubcategory.index') }}" class="menu-link">
                        <div class="text-truncate" data-i18n="CRM">Assets {{ $categorysettings->asset_category == 2 ? 'Sub' : '' }}  Category</div>
                    </a>
                </li> --}}
                <li class="menu-item {{ Route::currentRouteName() == 'asset.index' ? 'active' : '' }}">
                    <a href="{{ route('asset.index') }}" class="menu-link">
                        <div class="text-truncate" data-i18n="eCommerce">Current Assets</div>
                    </a>
                </li>
                <li class="menu-item {{ Route::currentRouteName() == 'assetFixed' ? 'active' : '' }}">
                    <a href="{{ route('assetFixed') }}" class="menu-link">
                        <div class="text-truncate" data-i18n="eCommerce">Fixed Assets</div>
                    </a>
                </li>
                <li class="menu-item {{ Route::currentRouteName() == 'assets.report' ? 'active' : '' }}">
                    <a href="{{ route('assets.report') }}"
                        class="menu-link">
                        <div class="text-truncate" data-i18n="eCommerce">Reports</div>
                    </a>
                </li>
            </ul>
        </li>   
        @endif

        @if(Auth::user()->access->liability == '1' || Auth::user()->access->liability == '2')
        <li class="menu-item  {{ Route::currentRouteName() == 'liability.index' || Route::currentRouteName() == 'liabilitycategory.index' || Route::currentRouteName() == 'liabilitysubcategory.index' || Route::currentRouteName() == 'liabilitysubsubcategory.index' || Route::currentRouteName() == 'liabilityFixed' ? 'active open' : '' }}  {{ Route::currentRouteName() == 'liability.report' ? 'active open' : '' }}">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                
                <i class="menu-icon1 tf-icons fa-solid fa-money-bill-wheat"></i>
                <div class="text-truncate" data-i18n="Dashboards">Liabilities Management</div>
            </a>
            <ul class="menu-sub">
                @if($categorysettings->liability_category == 2)
                <li class="menu-item {{ Route::currentRouteName() == 'liabilitycategory.index' ? 'active' : '' }}">
                    <a href="{{ route('liabilitycategory.index') }}" class="menu-link">
                        <div class="text-truncate" data-i18n="Analytics">Liabilities Category</div>
                    </a>
                </li>
                @endif
                
                <li class="menu-item {{ Route::currentRouteName() == 'liabilitysubcategory.index' ? 'active' : '' }}">
                    <a href="{{ route('liabilitysubcategory.index') }}" class="menu-link">
                        <div class="text-truncate" data-i18n="CRM">Liabilities {{ $categorysettings->liability_category == 2 ? 'Sub' : '' }} Category</div>
                    </a>
                </li>
                
                {{-- <li class="menu-item {{ Route::currentRouteName() == 'liabilitysubsubcategory.index' ? 'active' : '' }}">
                    <a href="{{ route('liabilitysubsubcategory.index') }}" class="menu-link">
                        <div class="text-truncate" data-i18n="CRM">Liabilities {{ $categorysettings->liability_category == 2 ? 'Sub' : '' }} Category</div>
                    </a>
                </li> --}}
                <li class="menu-item {{ Route::currentRouteName() == 'liability.index' ? 'active' : '' }}">
                    <a href="{{ route('liability.index') }}" class="menu-link">
                        <div class="text-truncate" data-i18n="eCommerce"> Short Term Liabilities</div>
                    </a>
                </li>
                <li class="menu-item {{ Route::currentRouteName() == 'liabilityFixed' ? 'active' : '' }}">
                    <a href="{{ route('liabilityFixed') }}" class="menu-link">
                        <div class="text-truncate" data-i18n="eCommerce">Long Term Liabilities</div>
                    </a>
                </li>
                <li class="menu-item {{ Route::currentRouteName() == 'liability.report' ? 'active' : '' }}">
                    <a href="{{ route('liability.report') }}"
                        class="menu-link">
                        <div class="text-truncate" data-i18n="eCommerce">Reports</div>
                    </a>
                </li>
            </ul>
        </li>
        @endif

        @if(Auth::user()->access->bankbook == '1' || Auth::user()->access->bankbook == '2')
        <li class="menu-item {{  in_array(Route::currentRouteName(), ['bankbook.index', 'banktransaction.index', 'bankschedule.index', 'bankbook.report'])  ? 'active open'  : '' }}">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                
                <i class="menu-icon1 tf-icons fa-solid fa-wallet"></i>
                <div class="text-truncate" data-i18n="Dashboards">BankBooks Management</div>
            </a>
            <ul class="menu-sub">
                <li class="menu-item {{ Route::currentRouteName() == 'bankbook.index' ? 'active' : '' }}">
                    <a href="{{ route('bankbook.index') }}" class="menu-link">
                        <div class="text-truncate" data-i18n="Analytics">BankBooks</div>
                    </a>
                </li>
                <li class="menu-item {{ Route::currentRouteName() == 'banktransaction.index' ? 'active' : '' }}">
                    <a href="{{ route('banktransaction.index') }}" class="menu-link">
                        <div class="text-truncate" data-i18n="CRM">Bank Transactions</div>
                    </a>
                </li>
                <li class="menu-item {{ Route::currentRouteName() == 'bankschedule.index' ? 'active' : '' }}">
                    <a href="{{ route('bankschedule.index') }}" class="menu-link">
                        <div class="text-truncate" data-i18n="CRM">Schedule Transfer</div>
                    </a>
                </li>
                <li class="menu-item {{ Route::currentRouteName() == 'bankbook.report' ? 'active' : '' }}">
                    <a href="{{ route('bankbook.report') }}"
                        class="menu-link">
                        <div class="text-truncate" data-i18n="eCommerce">Reports</div>
                    </a>
                </li>
            </ul>
        </li>
        @endif

        @if(Auth::user()->access->history_access == '1' || Auth::user()->access->history_access == '2')
        <li class="menu-item {{ Route::currentRouteName() == 'transaction.history' ? 'active' : '' }}">
            <a href="{{ route('transaction.history') }}"
                class="menu-link">
                <i class="menu-icon1 tf-icons fa-solid fa-clock-rotate-left"></i>
                <div class="text-truncate" data-i18n="Email">Transactions History </div>
            </a>
        </li>
        @endif

        @if(Auth::user()->access->accounts == '1' || Auth::user()->access->accounts == '2')
        <li class="menu-item   ">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                
                <i class="menu-icon1 tf-icons fa-solid fa-file-invoice"></i>
                <div class="text-truncate" data-i18n="Dashboards">Accounts</div>
            </a>
            <ul class="menu-sub">
                <li class="menu-item {{ Route::currentRouteName() == 'Cash-flow-statement' ? 'active' : '' }}">
                    <a href="{{ route('Cash-flow-statement') }}" class="menu-link">
                        <div class="text-truncate" data-i18n="Analytics">Cash Flow Statement</div>
                    </a>
                </li>
                <li class="menu-item {{ Route::currentRouteName() == 'income-expense-statement' ? 'active' : '' }}">
                    <a href="{{ route('income-expense-statement') }}" class="menu-link">
                        <div class="text-truncate" data-i18n="CRM">Income and Expenditure Statement</div>
                    </a>
                </li>
                <li class="menu-item {{ Route::currentRouteName() == 'financial-statement' ? 'active' : '' }}">
                    <a href="{{ route('financial-statement') }}"
                        class="menu-link">
                        <div class="text-truncate" data-i18n="eCommerce">Financial Statement</div>
                    </a>
                </li>
                <li class="menu-item {{ Route::currentRouteName() == 'detailed-financial-statement' ? 'active' : '' }}">
                    <a href="{{ route('detailed-financial-statement') }}"
                        class="menu-link">
                        <div class="text-truncate" data-i18n="eCommerce">Detailed Financial Statement</div>
                    </a>
                </li>
                
            </ul>
        </li>
        @endif
        
    </ul>


    <div class="aside-last-bar py-2">
        <p class="text-center text-secondary mb-1" style="font-size: 10px; font-weight: 500">
            @php
                $user = auth()->user();
            @endphp
            @if(auth()->user()->last_login_at)
                Last login : {{ \Carbon\Carbon::parse(auth()->user()->last_login_at)->format('d M Y, h:i A') }}
            @else
                First login: {{ \Carbon\Carbon::parse($user->created_at)->format('d M Y, h:i A') }}
            @endif
        </p>
        <h6 class="text-center text-primary mb-0" style="font-size: 12px ">Software Version 1.0</h6>
    </div>

</aside>
