<!-- Search Bar -->
<nav class="navbar" x-show="!isLoading" style="display: none;">
    <div class="logo_wrapper">
        <a href="/" class="logo">
            <img src="{{ asset('images/logo.svg') }}" alt="CORIM Logo" height="20">
        </a>
    </div>

    <div class="container container-custom-top">
        <form class="form-inline w-100 p-0 m-0" action="/" method="get">
            <div class="input-group">
                <div class="logo_wrapper_m pr-3">
                    <a href="/">
                        <img src="{{ asset('images/logo_icon.svg') }}" alt="CORIM Logo" class="logo_m">
                    </a>
                </div>

                <!-- Large input for larger screens -->
                <input
                    class="form-control form-control-lg bg-white d-none d-md-block border-0 flex-grow-1"
                    type="search"
                    placeholder="Search listings"
                    aria-label="Search"
                    name="search"
                    style="border-radius: 7px;"
                />

                <!-- Small input for smaller screens -->
                <input
                    class="form-control form-control-lg bg-white border-0 flex-grow-1 d-block d-md-none"
                    type="search"
                    placeholder="Search listings"
                    aria-label="Search"
                    name="search"
                    style="border-radius: 7px; margin-left: 25px;"
                />

                <button class="btn btn-lg search-btn border-0 input-group-text" type="submit">
                    <i class="fa fa-search"></i>
                </button>
            </div>
        </form>
    </div>
</nav>
