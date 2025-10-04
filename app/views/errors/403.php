<?php view('partials/header', ['title' => 'Access Denied']); ?>

<div class="container text-center" style="margin-top: 50px;">
    <div class="card">
        <div class="card-body">
            <h1 class="display-1" style="font-size: 5rem; color: var(--danger-color);">403</h1>
            <h2 class="mb-4">Access Denied</h2>
            <p class="lead">You do not have permission to view this page.</p>
            <p>Please contact the system administrator if you believe this is an error.</p>
            <a href="<?php echo base_url('dashboard'); ?>" class="btn btn-primary mt-3">Return to Dashboard</a>
        </div>
    </div>
</div>

<?php view('partials/footer'); ?>