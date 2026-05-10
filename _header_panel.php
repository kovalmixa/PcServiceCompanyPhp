<div class="glass-container">
    <div class="header-panel">
        <div class="logo" style="display: flex; align-items: center; gap: 20px;">
            <a asp-area="All" asp-controller="PcConfigurations" asp-action="Index"
               asp-route-pageSize='@(User.IsInRole("Admin") ? 29 : 30)'>PC Store</a>
        </div>

        <nav class="row-container" style="justify-content: flex-end; margin-right: 0;">
            <!-- @if (User.Identity != null && User.Identity.IsAuthenticated)
            {
                <a asp-area="Client" asp-controller="Profile" asp-action="Index" class="a-btn">Profile</a>
                @if (User.IsInRole("Customer"))
                {
                    <a asp-area="Client" asp-controller="Orders" asp-action="Cart" class="a-btn">Cart</a>
                }
                <a asp-area="@(User.IsInRole("Admin") ? "Staff" : "Client")"
                    asp-controller="@(User.IsInRole("Admin") ? "ComponentOrder" : "Orders")" asp-action="Index" class="a-btn">Orders</a>
                <a asp-area="All" asp-controller="PcConfigurations" asp-action="Index" asp-route-pageSize='@(User.IsInRole("Admin") ? 29 : 30)' class="a-btn">PC Configurations</a>
                <a asp-area="All" asp-controller="Components" asp-action="Index" asp-route-pageSize='@(User.IsInRole("Admin") ? 29 : 30)' class="a-btn">Components</a>
                @if (User.IsInRole("Staff") || User.IsInRole("Admin"))
                {
                    <button onclick="downloadReport()" class="a-btn">Log Reports</button>
                }
                @if (User.IsInRole("Admin"))
                {
                    <a asp-area="Admin" asp-controller="Suppliers" asp-action="Index" class="a-btn">Supliers</a>
                    <a asp-area="Admin" asp-controller="AdminPanel" asp-action="Index" class="a-btn">Admin panel</a>
                }
                <a asp-area="All" asp-controller="Auth" asp-action="Logout" class="red-btn">Logout</a>
            }
            else
            {
                <a asp-area="All" asp-controller="PcConfigurations" asp-action="Index" asp-route-pageSize='30' class="a-btn">PC Configurations</a>
                <a asp-area="All" asp-controller="Components" asp-action="Index" asp-route-pageSize='30' class="a-btn">Components</a>
                <a asp-area="All" asp-controller="Auth" asp-action="Login" class="a-btn">Login</a>
                <a asp-area="All" asp-controller="Auth" asp-action="Register" class="a-btn">Register</a>
            } -->
        </nav>
    </div>
</div>