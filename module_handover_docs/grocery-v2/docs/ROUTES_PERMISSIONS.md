# Grocery Module V2 Routing / Feature Controls

Shared router insertion: `routes/web.php` requires `routes/grocery_shop.php`.
`routes/vendor.php` requires `routes/grocery_vendor.php`; `routes/admin.php` requires `routes/grocery_admin.php`.

| Route | HTTP | Authentication | Rule |
|---|---|---|---|
| `/grocery` | GET | Public browse | `module + online_store + catalogue + pricing + inventory` ON |
| `/grocery/{business}/{branch}` | GET | Public browse | Same + approved vendor/business, active branch, accepting orders |
| `/grocery/{business}/{branch}/products/{product}` | GET | Public browse | Same, product active, unexpired positive stock |
| `/grocery/{business}/{branch}/basket` | GET | Public session | Same; display cart pricing |
| `/grocery/{business}/{branch}/basket/items` | POST/PATCH | Public session, CSRF & throttled | Store-scoped cart, revalidate product & stock |
| `/grocery/cart/clear` | POST | Public session, CSRF | Clear current basket only |
| `/grocery/{business}/{branch}/checkout` | GET/POST | `auth` + `EnsureCustomerAccount` | `online_orders` ON + same store gates, order placement POST throttled |
| `/grocery/my-orders` | GET | Authorized customer | User ID scoped, history stays accessible |
| `/grocery/my-orders/{order}` | GET | Authorized customer | User ID scoped, immutable snapshots |
| `/grocery/my-orders/{order}/cancel` | POST | Authorized customer | Pending only; stock restored even if OFF |
| `/vendor/businesses/{business}/grocery/online-orders` | GET | `auth` + `EnsureVendorAccount` | Approved Vendor owner business, branch scoped, online_orders ON |
| `/vendor/businesses/{business}/grocery/online-orders/{order}` | GET | Vendor owner | Same, business scoped |
| `/vendor/businesses/{business}/grocery/online-orders/{order}/status` | POST | Vendor owner | Valid transitions only; lock order, append events; rejection restores stock |
| `/vendor/businesses/{business}/grocery/products/{product}/presentation` | PATCH | Vendor owner | `catalogue` ON + branch/product owner check |
| `/vendor/businesses/{business}/grocery/...` V1 routes | GET/POST/PATCH | Vendor owner | Existing V1 flags, branch status & ownership |
| `/admin/grocery/features` | GET/POST | Super Admin | All global Grocery switches, audit logs |

Feature names: `module`, `catalogue`, `pricing`, `inventory`, `counter_sales`, `reports`, `online_store`, `online_orders`.

### Important admin semantics
- `online_store` OFF: new public browsing, cart updates, checkout OFF. Vendor online order processing can continue IF `online_orders` remains ON.
- `online_orders` OFF: new checkout and vendor processing OFF; pending customer self-cancellation still permitted. History remains.
- Branch preference OFF overrides an Admin ON (never opposite). Missing setting = inherit ON for a globally enabled feature.
- Never assume a visible navigation link grants permission.
- Unapproved vendors/businesses, inactive branches, accept-orders OFF cannot take new orders.

### Not included
Employee-specific Grocery cashier access and full platform-wide feature-control registry; those need the Master integration permissions design.
