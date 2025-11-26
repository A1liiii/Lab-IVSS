<div class="admin-sidebar" style="
  width:260px;
  background: var(--surface-color);
  border-right: 1px solid color-mix(in srgb, var(--default-color), transparent 85%);
  padding: 20px;
">

  <h3>IVSS Admin</h3>

  <a href="/lab-ivss/index.php?page=admin-dashboard" class="<?= (isset($active) && $active === 'dashboard') ? 'active' : '' ?>">
    <i class="bi bi-speedometer2"></i> Dashboard
  </a>

  <a href="/lab-ivss/index.php?page=admin-user" class="<?= (isset($active) && $active === 'user') ? 'active' : '' ?>">
    <i class="bi bi-people"></i> User Management
  </a>

  <a href="/lab-ivss/index.php?page=admin-approvals" class="<?= (isset($active) && $active === 'approvals') ? 'active' : '' ?>">
    <i class="bi bi-person-check"></i> Approvals
  </a>

  <a href="/lab-ivss/index.php?page=admin-logs" class="<?= (isset($active) && $active === 'logs') ? 'active' : '' ?>">
    <i class="bi bi-clipboard-data"></i> Logs
  </a>

  <a href="/lab-ivss/public/logout.php" style="margin-top:20px;">
    <i class="bi bi-box-arrow-right"></i> Logout
  </a>
</div>
