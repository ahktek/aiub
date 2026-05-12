<h2 style="margin-bottom: 2rem;">Manage Products</h2>

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
                <h3 style="margin: 0;">Product List</h3>
            </div>
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>Image</th>
                            <th>Name</th>
                            <th>Category</th>
                            <th>Price</th>
                            <th>Stock</th>
                            <th>Rating</th>
                            <th>Status</th>
                            <th style="text-align: right;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($products as $p): ?>
                        <tr style="<?php echo ($p['stock_qty'] <= 5) ? 'background-color: rgba(239, 68, 68, 0.05);' : ''; ?>">
                            <td style="width: 60px;">
                                <?php if ($p['primary_image_path']): ?>
                                    <img src="uploads/products/<?php echo htmlspecialchars($p['primary_image_path']); ?>" alt="img" class="table-img">
                                <?php else: ?>
                                    <div class="table-img" style="background-color: #eee; display: flex; align-items: center; justify-content: center; font-size: 0.7rem; color: #999;">No Img</div>
                                <?php endif; ?>
                            </td>
                            <td style="font-weight: 500; color: var(--text-primary);"><?php echo htmlspecialchars($p['name']); ?></td>
                            <td><?php echo htmlspecialchars($p['category_name']); ?></td>
                            <td style="font-weight: 500;">$<?php echo number_format($p['price'], 2); ?></td>
                            <td>
                                <?php if ($p['stock_qty'] <= 5): ?>
                                    <span style="color: var(--status-danger); font-weight: 600;"><?php echo $p['stock_qty']; ?></span>
                                <?php else: ?>
                                    <span><?php echo $p['stock_qty']; ?></span>
                                <?php endif; ?>
                            </td>
                            <td><span style="color: #fbbf24;">★</span> <?php echo number_format($p['avg_rating'], 1); ?></td>
                            <td>
                                <button type="button" 
                                        class="btn toggle-availability badge <?php echo $p['is_available'] ? 'badge-success' : 'badge-danger'; ?>" 
                                        data-id="<?php echo $p['id']; ?>"
                                        style="border: none; cursor: pointer; padding: 0.4rem 0.6rem;">
                                    <?php echo $p['is_available'] ? 'Active' : 'Inactive'; ?>
                                </button>
                            </td>
                            <td style="text-align: right; white-space: nowrap;">
                                <a href="?route=admin/products&edit_id=<?php echo $p['id']; ?>" class="btn btn-outline btn-sm" style="margin-right: 0.5rem;">Edit</a>
                                <form action="?route=admin/products" method="POST" style="display:inline;" onsubmit="return confirm('Delete this product?');">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="id" value="<?php echo $p['id']; ?>">
                                    <button type="submit" class="btn btn-outline btn-sm" style="color: var(--status-danger); border-color: transparent;">Delete</button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if (empty($products)): ?>
                        <tr>
                            <td colspan="8" style="text-align: center; padding: 3rem; color: var(--text-secondary);">No products found.</td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="admin-sidebar">
        <div class="card" style="position: sticky; top: 2rem;">
            <h3 style="margin-bottom: 1.5rem; border-bottom: 1px solid var(--border-color); padding-bottom: 1rem;">
                <?php echo $edit_product ? 'Edit Product' : 'Create Product'; ?>
            </h3>
            
            <?php if ($edit_product): ?>
                <a href="?route=admin/products" class="btn btn-outline btn-block" style="margin-bottom: 1.5rem; text-align: center;">&larr; Cancel Edit</a>
            <?php endif; ?>

            <form action="?route=admin/products" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="action" value="<?php echo $edit_product ? 'edit' : 'create'; ?>">
                <?php if ($edit_product): ?>
                    <input type="hidden" name="id" value="<?php echo $edit_product['id']; ?>">
                <?php endif; ?>

                <div class="form-group">
                    <label for="name" class="form-label">Product Name</label>
                    <input type="text" id="name" name="name" class="form-control" value="<?php echo $edit_product ? htmlspecialchars($edit_product['name']) : ''; ?>" required>
                </div>

                <div class="form-group">
                    <label for="category_id" class="form-label">Category</label>
                    <select id="category_id" name="category_id" class="form-control" required>
                        <option value="">-- Select Category --</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?php echo $cat['id']; ?>" <?php echo ($edit_product && $edit_product['category_id'] == $cat['id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($cat['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="description" class="form-label">Description</label>
                    <textarea id="description" name="description" class="form-control" rows="4"><?php echo $edit_product ? htmlspecialchars($edit_product['description']) : ''; ?></textarea>
                </div>

                <div class="form-group">
                    <label for="price" class="form-label">Price ($)</label>
                    <input type="number" step="0.01" id="price" name="price" class="form-control" value="<?php echo $edit_product ? $edit_product['price'] : ''; ?>" required>
                </div>

                <div class="form-group">
                    <label for="stock_qty" class="form-label">Stock Quantity</label>
                    <input type="number" id="stock_qty" name="stock_qty" class="form-control" value="<?php echo $edit_product ? $edit_product['stock_qty'] : '0'; ?>" required>
                </div>

                <div class="form-group">
                    <label for="image" class="form-label">Primary Image <?php echo $edit_product ? '<span style="color: var(--text-secondary); font-weight: normal; font-size: 0.8rem;">(Optional)</span>' : ''; ?></label>
                    <input type="file" id="image" name="image" class="form-control" accept=".jpg, .jpeg, .png" style="padding: 0.5rem;" <?php echo $edit_product ? '' : 'required'; ?>>
                    <?php if ($edit_product && $edit_product['primary_image_path']): ?>
                        <div style="margin-top: 1rem; padding: 1rem; border: 1px solid var(--border-color); border-radius: var(--border-radius); text-align: center;">
                            <span style="display: block; margin-bottom: 0.5rem; color: var(--text-secondary); font-size: 0.9rem;">Current Image</span>
                            <img src="uploads/products/<?php echo htmlspecialchars($edit_product['primary_image_path']); ?>" style="width: 80px; height: 80px; object-fit: cover; border-radius: 4px; box-shadow: var(--shadow-sm);">
                        </div>
                    <?php endif; ?>
                </div>

                <button type="submit" class="btn btn-primary btn-block" style="padding: 1rem; font-size: 1.05rem; margin-top: 1rem;">
                    <?php echo $edit_product ? 'Save Changes' : 'Create Product'; ?>
                </button>
            </form>
        </div>
    </div>
</div>
