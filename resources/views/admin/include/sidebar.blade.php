<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
    <div class="app-brand demo">
        <a href="{{ route('admin.dashboard') }}" class="app-brand-link">
            <img src="{{ asset('admin-assets/img/logo.png') }}" alt="Logo" class="app-brand-logo demo" height="100%" width="40%" />
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

        <li class="menu-item {{ Route::currentRouteName() == 'contact.index' ? 'active' : '' }}">
            <a href="{{ route('contact.index') }}"
                class="menu-link">
                <i class="menu-icon tf-icons bx bx-home-smile"></i>
                <div class="text-truncate" data-i18n="Email">Contact </div>
            </a>
        </li>

        <li class="menu-item  {{ Route::currentRouteName() == 'income.index' || Route::currentRouteName() == 'incomecategory.index' || Route::currentRouteName() == 'incomesubcategory.index' ? 'active open' : '' }}  ">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons bx bx-home-smile"></i>
                <div class="text-truncate" data-i18n="Dashboards">Incomes Managment</div>
            </a>
            <ul class="menu-sub">
                <li class="menu-item {{ Route::currentRouteName() == 'incomecategory.index' ? 'active' : '' }}">
                    <a href="{{ route('incomecategory.index') }}" class="menu-link">
                        <div class="text-truncate" data-i18n="Analytics">Incomes Category</div>
                    </a>
                </li>
                <li class="menu-item {{ Route::currentRouteName() == 'incomesubcategory.index' ? 'active' : '' }}">
                    <a href="{{ route('incomesubcategory.index') }}"
                        class="menu-link">
                        <div class="text-truncate" data-i18n="CRM">Incomes Sub Category</div>
                    </a>
                </li>
                <li class="menu-item {{ Route::currentRouteName() == 'income.index' ? 'active' : '' }}">
                    <a href="{{ route('income.index') }}"
                        class="menu-link">
                        <div class="text-truncate" data-i18n="eCommerce">Incomes</div>
                        
                    </a>
                </li>
            </ul>
        </li>

        <li class="menu-item  {{ Route::currentRouteName() == 'expense.index' || Route::currentRouteName() == 'expensecategory.index' || Route::currentRouteName() == 'expensesubcategory.index' ? 'active open' : '' }}  ">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons bx bx-home-smile"></i>
                <div class="text-truncate" data-i18n="Dashboards">Expenses Managment</div>
            </a>
            <ul class="menu-sub">
                <li class="menu-item {{ Route::currentRouteName() == 'expensecategory.index' ? 'active' : '' }}">
                    <a href="{{ route('expensecategory.index') }}" class="menu-link">
                        <div class="text-truncate" data-i18n="Analytics">Expenses Category</div>
                    </a>
                </li>
                <li class="menu-item {{ Route::currentRouteName() == 'expensesubcategory.index' ? 'active' : '' }}">
                    <a href="{{ route('expensesubcategory.index') }}"
                         class="menu-link">
                        <div class="text-truncate" data-i18n="CRM">Expenses Sub Category</div>
                    </a>
                </li>
                <li class="menu-item {{ Route::currentRouteName() == 'expense.index' ? 'active' : '' }}">
                    <a href="{{ route('expense.index') }}"
                        class="menu-link">
                        <div class="text-truncate" data-i18n="eCommerce">Expenses</div>
                        
                    </a>
                </li>
            </ul>
        </li>

        <li class="menu-item  {{ Route::currentRouteName() == 'investment.index' || Route::currentRouteName() == 'investmentcategory.index' || Route::currentRouteName() == 'investmentsubcategory.index' ? 'active open' : '' }}  ">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons bx bx-home-smile"></i>
                <div class="text-truncate" data-i18n="Dashboards">Investments Management</div>
            </a>
            <ul class="menu-sub">
                <li class="menu-item {{ Route::currentRouteName() == 'investmentcategory.index' ? 'active' : '' }}">
                    <a href="{{ route('investmentcategory.index') }}" class="menu-link">
                        <div class="text-truncate" data-i18n="Analytics">Investments Category</div>
                    </a>
                </li>
                <li class="menu-item {{ Route::currentRouteName() == 'investmentsubcategory.index' ? 'active' : '' }}">
                    <a href="{{ route('investmentsubcategory.index') }}" class="menu-link">
                        <div class="text-truncate" data-i18n="CRM">Investments Sub Category</div>
                    </a>
                </li>
                <li class="menu-item {{ Route::currentRouteName() == 'investment.index' ? 'active' : '' }}">
                    <a href="{{ route('investment.index') }}" class="menu-link">
                        <div class="text-truncate" data-i18n="eCommerce">Investments</div>
                    </a>
                </li>
            </ul>
        </li>

        <li class="menu-item  {{ Route::currentRouteName() == 'asset.index' || Route::currentRouteName() == 'assetcategory.index' || Route::currentRouteName() == 'assetsubcategory.index' || Route::currentRouteName() == 'assetsubsubcategory.index' ? 'active open' : '' }}  ">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons bx bx-home-smile"></i>
                <div class="text-truncate" data-i18n="Dashboards">Assets Management</div>
            </a>
            <ul class="menu-sub">
                <li class="menu-item {{ Route::currentRouteName() == 'assetcategory.index' ? 'active' : '' }}">
                    <a href="{{ route('assetcategory.index') }}" class="menu-link">
                        <div class="text-truncate" data-i18n="Analytics">Assets Category</div>
                    </a>
                </li>
                <li class="menu-item {{ Route::currentRouteName() == 'assetsubcategory.index' ? 'active' : '' }}">
                    <a href="{{ route('assetsubcategory.index') }}" class="menu-link">
                        <div class="text-truncate" data-i18n="CRM">Assets Sub Category</div>
                    </a>
                </li>
                <li class="menu-item {{ Route::currentRouteName() == 'assetsubsubcategory.index' ? 'active' : '' }}">
                    <a href="{{ route('assetsubsubcategory.index') }}" class="menu-link">
                        <div class="text-truncate" data-i18n="CRM">Assets Sub Sub Category</div>
                    </a>
                </li>
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
            </ul>
        </li>   

        <li class="menu-item  {{ Route::currentRouteName() == 'liability.index' || Route::currentRouteName() == 'liabilitycategory.index' || Route::currentRouteName() == 'liabilitysubcategory.index' ? 'active open' : '' }}  ">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons bx bx-home-smile"></i>
                <div class="text-truncate" data-i18n="Dashboards">Liabilities Management</div>
            </a>
            <ul class="menu-sub">
                <li class="menu-item {{ Route::currentRouteName() == 'liabilitycategory.index' ? 'active' : '' }}">
                    <a href="{{ route('liabilitycategory.index') }}" class="menu-link">
                        <div class="text-truncate" data-i18n="Analytics">Liabilities Category</div>
                    </a>
                </li>
                <li class="menu-item {{ Route::currentRouteName() == 'liabilitysubcategory.index' ? 'active' : '' }}">
                    <a href="{{ route('liabilitysubcategory.index') }}" class="menu-link">
                        <div class="text-truncate" data-i18n="CRM">Liabilities Sub Category</div>
                    </a>
                </li>
                <li class="menu-item {{ Route::currentRouteName() == 'liabilitysubsubcategory.index' ? 'active' : '' }}">
                    <a href="{{ route('liabilitysubsubcategory.index') }}" class="menu-link">
                        <div class="text-truncate" data-i18n="CRM">Liabilities Sub Sub Category</div>
                    </a>
                </li>
                <li class="menu-item {{ Route::currentRouteName() == 'liability.index' ? 'active' : '' }}">
                    <a href="{{ route('liability.index') }}" class="menu-link">
                        <div class="text-truncate" data-i18n="eCommerce">Current Liabilities</div>
                    </a>
                </li>
                <li class="menu-item {{ Route::currentRouteName() == 'liabilityFixed' ? 'active' : '' }}">
                    <a href="{{ route('liabilityFixed') }}" class="menu-link">
                        <div class="text-truncate" data-i18n="eCommerce">Fixed Liabilities</div>
                    </a>
                </li>
            </ul>
        </li>

        <li class="menu-item {{ Route::currentRouteName() == 'bankbook.index' ? 'active' : '' }}">
            <a href="{{ route('bankbook.index') }}"
                target="_blank" class="menu-link">
                <i class="menu-icon tf-icons bx bx-home-smile"></i>
                <div class="text-truncate" data-i18n="Email">BankBooks</div>
            </a>
        </li>
        
    </ul>
</aside>
