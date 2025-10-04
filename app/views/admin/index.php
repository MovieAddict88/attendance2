<?php view('partials/header', ['title' => 'Admin Dashboard']); ?>
<?php view('partials/sidebar'); ?>

<div class="container-fluid">
    <div class="card">
        <div class="card-header">
            <h1>Admin Dashboard</h1>
        </div>
        <div class="card-body">
            <p>Welcome, Super Admin! Here you can manage all aspects of the school.</p>

            <div class="row mt-4">
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body text-center">
                            <i class="fas fa-users-cog fa-3x mb-3"></i>
                            <h5 class="card-title">Manage Teachers</h5>
                            <p class="card-text">Add, edit, and remove teacher accounts.</p>
                            <a href="#" class="btn btn-primary">Go to Teachers</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body text-center">
                            <i class="fas fa-user-graduate fa-3x mb-3"></i>
                            <h5 class="card-title">Manage Students</h5>
                            <p class="card-text">View and manage student records across all classes.</p>
                            <a href="#" class="btn btn-primary">Go to Students</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body text-center">
                            <i class="fas fa-cog fa-3x mb-3"></i>
                            <h5 class="card-title">System Settings</h5>
                            <p class="card-text">Configure school year, terms, and global settings.</p>
                            <a href="#" class="btn btn-primary">Go to Settings</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php view('partials/footer'); ?>