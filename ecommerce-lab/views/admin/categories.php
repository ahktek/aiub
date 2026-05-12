<h2 style="margin-bottom: 2rem;">Manage Categories</h2>

<?php if (!empty($error)): ?>
    <div class="alert alert-danger" style="margin-bottom: 1.5rem;"><?php echo htmlspecialchars($error); ?></div>
<?php endif; ?>
<?php if (!empty($success)): ?>
    <div class="alert alert-success" style="margin-bottom: 1.5rem;"><?php echo htmlspecialchars($success); ?></div>
<?php endif; ?>

<div class="admin-layout">
    <div class="admin-main">
        <div class="card" style="padding: 0; overflow: hidden;">
            <div style="padding: 1.5rem; border-bottom: 1px solid var(--border-color); display: flex; justify-content: space-between; align-items: center;">
                <h3 style="margin: 0;">Category List</h3>
            </div>
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Parent Category</th>
                            <th style="text-align: right;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($categories as $cat): ?>
                        <tr>
                            <td><?php echo $cat['id']; ?></td>
                            <td style="font-weight: 500; color: var(--text-primary);"><?php echo htmlspecialchars($cat['name']); ?></td>
                            <td><?php echo $cat['parent_name'] ? htmlspecialchars($cat['parent_name']) : '<em style="color: var(--text-secondary);">None (Top Level)</em>'; ?></td>
                            <td style="text-align: right;">
                                <form action="?route=admin/categories" method="POST" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this category?');">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="id" value="<?php echo $cat['id']; ?>">
                                    <button type="submit" class="btn btn-outline btn-sm" style="color: var(--status-danger); border-color: transparent;">Delete</button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if (empty($categories)): ?>
                        <tr>
                            <td colspan="4" style="text-align: center; padding: 3rem; color: var(--text-secondary);">No categories found.</td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="admin-sidebar">
        <div class="card" style="position: sticky; top: 2rem;">
            <h3 style="margin-bottom: 1.5rem; border-bottom: 1px solid var(--border-color); padding-bottom: 1rem;">Create Category</h3>
            
            <form action="?route=admin/categories" method="POST">
                <input type="hidden" name="action" value="create">
                
                <div class="form-group">
                    <label for="name" class="form-label">Category Name</label>
                    <input type="text" id="name" name="name" class="form-control" required>
                </div>

                <div class="form-group">
                    <label for="parent_id" class="form-label">Parent Category <span style="color: var(--text-secondary); font-weight: normal;">(Optional)</span></label>
                    <select id="parent_id" name="parent_id" class="form-control">
                        <option value="">-- None (Top Level) --</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?php echo $cat['id']; ?>">
                                <?php echo htmlspecialchars($cat['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <button type="submit" class="btn btn-primary btn-block" style="padding: 1rem; font-size: 1.05rem; margin-top: 1rem;">Create Category</button>
            </form>
        </div>
    </div>
</div>
