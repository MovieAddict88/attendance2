<aside class="sidebar" id="sidebar">
    <div class="sidebar-header">
        <a href="<?php echo base_url(); ?>" class="app-logo">SMS</a>
    </div>
    <nav class="sidebar-nav">
        <ul>
            <!-- Navigation links will be dynamically generated based on user role -->
            <li>
                <a href="<?php echo base_url('dashboard'); ?>" class="active">
                    <i class="fas fa-tachometer-alt"></i>
                    <span>Dashboard</span>
                </a>
            </li>

            <!-- Super Admin Links -->
            <li>
                <a href="#">
                    <i class="fas fa-users-cog"></i>
                    <span>Manage Teachers</span>
                </a>
            </li>
            <li>
                <a href="#">
                    <i class="fas fa-user-graduate"></i>
                    <span>Manage Students</span>
                </a>
            </li>
            <li>
                <a href="#">
                    <i class="fas fa-bullhorn"></i>
                    <span>Announcements</span>
                </a>
            </li>
            <li>
                <a href="#">
                    <i class="fas fa-cog"></i>
                    <span>Settings</span>
                </a>
            </li>

            <!-- Teacher Links -->
            <li>
                <a href="#">
                    <i class="fas fa-clipboard-user"></i>
                    <span>Attendance</span>
                </a>
            </li>
            <li>
                <a href="#">
                    <i class="fas fa-file-alt"></i>
                    <span>Quizzes</span>
                </a>
            </li>
            <li>
                <a href="#">
                    <i class="fas fa-tasks"></i>
                    <span>Assignments</span>
                </a>
            </li>

            <!-- Student/Parent Links -->
            <li>
                <a href="#">
                    <i class="fas fa-chart-line"></i>
                    <span>Grades</span>
                </a>
            </li>
             <li>
                <a href="#">
                    <i class="fas fa-envelope"></i>
                    <span>Messages</span>
                </a>
            </li>
        </ul>
    </nav>
</aside>