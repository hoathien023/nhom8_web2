<!-- Instagram End -->
<div style="border: 1px solid #0A68FF;"></div>
<!-- Footer Section Begin -->
<footer class="footer">
    <div class="container">
        <div class="row">
            <div class="col-lg-4 col-md-6 col-sm-7">
                <div class="footer__about">
                    <div class="footer__logo">
                        <a href="index.php"><img src="upload/logo/logo-trai-cay.png" alt=""></a>
                    </div>
                    <p>Chào mừng bạn đến với Khatoco nơi cung cấp quần áo chất lượng</p>

                </div>
            </div>
            <div class="col-lg-2 col-md-3 col-sm-5">
                <div class="footer__widget">
                    <h6>ĐƯỜNG DẪN</h6>
                    <ul>
                        <li><a href="index.php?url=lien-he">Về chúng tôi</a></li>
                        <li><a href="index.php?url=bai-viet">Blogs</a></li>
                        <li><a href="index.php?url=lien-he">Liên hệ</a></li>
                        <li><a href="index.php?url=faq">FAQ</a></li>
                    </ul>
                </div>
            </div>
            <div class="col-lg-2 col-md-3 col-sm-4">
                <div class="footer__widget">
                    <h6>tÀI khoẢN</h6>
                    <ul>
                        <li><a href="index.php?url=thong-tin-tai-khoan">Tài khoản của tôi</a></li>
                        <li><a href="index.php?url=don-hang">Theo dõi đơn hàng</a></li>
                        <li><a href="index.php?url=thu-tuc-thanh-toan">Thủ tục thanh toán</a></li>
                        <li><a href="index.php?url=yeu-thich">Danh sách yêu thích</a></li>
                    </ul>
                </div>
            </div>
            <div class="col-lg-4 col-md-8 col-sm-8">
                <div class="footer__newslatter">
                    <h6>BẢN TIN</h6>
                    <form action="#">
                        <input type="text" placeholder="Email">
                        <button type="submit" class="site-btn">Theo dõi</button>
                    </form>
                    <div class="footer__payment">
                        <a href="#"><img src="public/img/payment/payment-1.png" alt=""></a>
                        <a href="#"><img src="public/img/payment/payment-2.png" alt=""></a>
                        <a href="#"><img src="public/img/payment/payment-3.png" alt=""></a>
                        <a href="#"><img src="public/img/payment/payment-4.png" alt=""></a>
                        <a href="#"><img src="public/img/payment/payment-5.png" alt=""></a>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12">
                <!-- Link back to Colorlib can't be removed. Template is licensed under CC BY 3.0. -->
                <div class="footer__copyright__text">
                    <p>Copyright &copy; <script>
                        document.write(new Date().getFullYear());
                        </script> All rights reserved | This template is made with <i class="fa fa-heart"
                            aria-hidden="true"></i> by <a href="#" target="_blank">Nhóm 8</a></p>
                </div>
                <!-- Link back to Colorlib can't be removed. Template is licensed under CC BY 3.0. -->
            </div>
        </div>
    </div>
</footer>
<!-- Footer Section End -->

<!-- Search Begin -->
<div class="search-model">
    <div class="h-100 d-flex align-items-center justify-content-center">
        <div class="search-close-switch">+</div>
        <form action="tim-kiem" method="get" class="search-model-form">
            <input type="search" name="query" id="search-input" placeholder="TÌM KIẾM.....">
            <div style="margin-top: 22px; text-align: center;">
                <a href="index.php?url=cua-hang"
                    style="display:inline-block; padding:10px 18px; border-radius:10px; background:linear-gradient(135deg,#e7f3ff,#d8ecff); color:#0a5ec2; font-weight:700; border:1px solid #b9daff; box-shadow:0 6px 16px rgba(10,94,194,.15);">
                    Tìm kiếm nâng cao
                </a>
            </div>
        </form>
    </div>
</div>
<!-- Search End -->

<!-- Toatr -->
<script>
(function() {
    if (!window.jQuery || !window.toastr) return;
    jQuery(function($) {
        $("#toastr-success-top-right").on("click", function() {
            toastr.success("1 sản phẩm đã thêm vào giỏ", "Thành công", {
                closeButton: true,
                debug: false,
                newestOnTop: false,
                progressBar: true,
                positionClass: "toast-top-right",
                preventDuplicates: false,
                onclick: null,
                showDuration: "300",
                hideDuration: "1000",
                timeOut: "5000",
                extendedTimeOut: "1000",
                showEasing: "swing",
                hideEasing: "linear",
                showMethod: "fadeIn",
                hideMethod: "fadeOut"
            });
        });
    });
})();
</script>

<?php if (!empty($_SESSION['cart_toast_success'])) { ?>
<script>
window.__cartToastSuccess = "<?=htmlspecialchars($_SESSION['cart_toast_success'], ENT_QUOTES)?>";
</script>
<?php unset($_SESSION['cart_toast_success']); } ?>

<!-- Js Plugins -->

<script src="public/js/jquery-3.3.1.min.js"></script>
<script src="public/js/bootstrap.min.js"></script>
<script src="public/js/jquery.magnific-popup.min.js"></script>
<script src="public/js/jquery-ui.min.js"></script>
<script src="public/js/mixitup.min.js"></script>
<script src="public/js/jquery.countdown.min.js"></script>
<script src="public/js/jquery.slicknav.js"></script>
<script src="public/js/owl.carousel.min.js"></script>
<script src="public/js/jquery.nicescroll.min.js"></script>

<script src="public/js/main.js?v=20260408-1"></script>

<script>
(function() {
    function showCenterCartNotice(message, isError) {
        var old = document.getElementById('cart-center-toast');
        if (old) old.remove();

        var wrap = document.createElement('div');
        wrap.id = 'cart-center-toast';
        wrap.style.position = 'fixed';
        wrap.style.top = '88px';
        wrap.style.left = '50%';
        wrap.style.transform = 'translateX(-50%)';
        wrap.style.zIndex = '99999';
        wrap.style.minWidth = '360px';
        wrap.style.maxWidth = '90vw';
        wrap.style.padding = '14px 20px';
        wrap.style.borderRadius = '10px';
        wrap.style.boxShadow = '0 10px 26px rgba(0,0,0,.18)';
        wrap.style.textAlign = 'center';
        wrap.style.fontSize = '20px';
        wrap.style.fontWeight = '700';
        wrap.style.lineHeight = '1.3';
        wrap.style.color = '#fff';
        wrap.style.background = isError ? 'linear-gradient(90deg,#ef4444,#dc2626)' : 'linear-gradient(90deg,#22c55e,#16a34a)';
        wrap.style.border = '1px solid rgba(255,255,255,.2)';
        wrap.textContent = message || 'Đã thêm sản phẩm vào giỏ hàng thành công :3';
        document.body.appendChild(wrap);

        setTimeout(function() {
            if (wrap && wrap.parentNode) wrap.parentNode.removeChild(wrap);
        }, 1800);
    }

    function updateCartCountUI(count) {
        if (typeof count !== 'number' || isNaN(count) || count < 0) return;
        var normalized = String(parseInt(count, 10));
        document.querySelectorAll('.js-cart-count').forEach(function(el) {
            el.textContent = normalized;
        });
        document.querySelectorAll('.js-cart-count-text').forEach(function(el) {
            el.textContent = normalized + ' sản phẩm thêm vào giỏ';
        });
    }

    function formatMoneyVND(num) {
        return Number(num || 0).toLocaleString('en-US') + '₫';
    }

    function updateMiniCartTotal(total) {
        if (typeof total !== 'number' || isNaN(total) || total < 0) return;
        document.querySelectorAll('.js-mini-cart-total').forEach(function(el) {
            el.textContent = formatMoneyVND(total);
        });
    }

    function refreshMiniCartPreview() {
        // Tải lại HTML mini-cart từ server để đồng bộ item + tổng tiền.
        fetch('index.php?url=gio-hang', {
            method: 'GET',
            credentials: 'same-origin'
        })
        .then(function(res) { return res.text(); })
        .then(function(html) {
            var parser = new DOMParser();
            var doc = parser.parseFromString(html, 'text/html');
            var freshCart = doc.querySelector('.shopping-cart');
            var currentCart = document.querySelector('.shopping-cart');
            if (freshCart && currentCart) {
                currentCart.innerHTML = freshCart.innerHTML;
            }
        })
        .catch(function() {});
    }

    function postMiniCartAction(payload) {
        var body = new URLSearchParams();
        Object.keys(payload || {}).forEach(function(key) {
            body.append(key, String(payload[key]));
        });
        return fetch('index.php?url=gio-hang', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'
            },
            body: body.toString()
        }).then(function(res) { return res.json(); });
    }

    function bindMiniCartActions() {
        document.addEventListener('click', function(e) {
            var minusBtn = e.target.closest('.js-mini-cart-minus');
            var plusBtn = e.target.closest('.js-mini-cart-plus');
            var removeBtn = e.target.closest('.js-mini-cart-remove');
            var checkoutBtn = e.target.closest('.js-mini-cart-checkout');
            if (!minusBtn && !plusBtn && !removeBtn && !checkoutBtn) return;
            e.preventDefault();
            e.stopPropagation();

            if (checkoutBtn) {
                var rows = Array.prototype.slice.call(document.querySelectorAll('.mini-cart-item[data-product-id]'));
                var productIds = rows.map(function(row) {
                    return Number(row.getAttribute('data-product-id') || 0);
                }).filter(function(id) { return id > 0; });
                if (!productIds.length) {
                    showCenterCartNotice('Giỏ hàng đang trống.', true);
                    return;
                }
                var form = document.createElement('form');
                form.method = 'post';
                form.action = 'index.php?url=thanh-toan';
                productIds.forEach(function(id) {
                    var input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'selected_product_ids[]';
                    input.value = String(id);
                    form.appendChild(input);
                });
                document.body.appendChild(form);
                form.submit();
                return;
            }

            if (removeBtn) {
                var cartId = Number(removeBtn.getAttribute('data-cart-id') || 0);
                if (cartId <= 0) return;
                var row = removeBtn.closest('.mini-cart-item');
                if (row) row.style.opacity = '0.35';
                postMiniCartAction({
                    ajax_remove_cart_item: 1,
                    cart_id: cartId
                }).then(function(data) {
                    if (data && data.ok) {
                        if (row) row.remove();
                        if (typeof data.cart_count !== 'undefined') {
                            updateCartCountUI(Number(data.cart_count));
                        }
                        if (typeof data.cart_total !== 'undefined') {
                            updateMiniCartTotal(Number(data.cart_total));
                        }
                        if (!document.querySelector('.mini-cart-item[data-product-id]')) {
                            refreshMiniCartPreview();
                        }
                    }
                }).catch(function() {
                    if (row) row.style.opacity = '1';
                });
                return;
            }

            var controls = (minusBtn || plusBtn).closest('.mini-cart-qty-controls');
            if (!controls) return;
            var productId = Number(controls.getAttribute('data-product-id') || 0);
            if (productId <= 0) return;
            var qtyEl = controls.querySelector('.mini-qty-value');
            var currentQty = Number(qtyEl ? qtyEl.textContent : 1);
            if (isNaN(currentQty) || currentQty < 1) currentQty = 1;
            var nextQty = plusBtn ? currentQty + 1 : Math.max(1, currentQty - 1);

            if (qtyEl) qtyEl.textContent = String(nextQty);
            if (minusBtn) minusBtn.disabled = true;
            if (plusBtn) plusBtn.disabled = true;

            postMiniCartAction({
                ajax_update_qty: 1,
                product_id: productId,
                quantity: nextQty
            }).then(function(data) {
                if (!data || !data.ok) {
                    if (qtyEl) qtyEl.textContent = String(currentQty);
                    return;
                }
                if (qtyEl && typeof data.quantity !== 'undefined') {
                    qtyEl.textContent = String(Number(data.quantity) || 1);
                }
                if (typeof data.cart_count !== 'undefined') {
                    updateCartCountUI(Number(data.cart_count));
                }
                if (typeof data.cart_total !== 'undefined') {
                    updateMiniCartTotal(Number(data.cart_total));
                }
            }).catch(function() {
                if (qtyEl) qtyEl.textContent = String(currentQty);
            }).finally(function() {
                if (minusBtn) minusBtn.disabled = false;
                if (plusBtn) plusBtn.disabled = false;
            });
        }, true);
    }

    function hookAjaxAddToCart() {
        function handleAddToCartForm(form) {
            if (!form) return;
            if (form.dataset.ajaxBypass === '1') {
                return;
            }

            var fd = new FormData(form);
            fd.set('add_to_cart', '1');
            fd.set('ajax_add_to_cart', '1');
            fd.set('redirect_to', window.location.href);

            var body = new URLSearchParams();
            fd.forEach(function(value, key) {
                body.append(key, value);
            });

            fetch(form.getAttribute('action') || 'index.php?url=gio-hang', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'
                },
                body: body.toString()
            })
            .then(function(res) { return res.text(); })
            .then(function(raw) {
                var data = null;
                try {
                    data = JSON.parse(raw);
                } catch (err) {
                    // Fallback: nếu không parse được JSON thì submit form kiểu cũ để vẫn thêm giỏ.
                    form.dataset.ajaxBypass = '1';
                    form.submit();
                    return;
                }
                if (data && data.ok) {
                    showCenterCartNotice(data.message || 'Bạn đã thêm sản phẩm vào giỏ hàng thành công :3', false);
                    if (typeof data.cart_count !== 'undefined') {
                        updateCartCountUI(Number(data.cart_count));
                    }
                    if (typeof data.cart_total !== 'undefined') {
                        updateMiniCartTotal(Number(data.cart_total));
                    }
                    refreshMiniCartPreview();
                } else {
                    showCenterCartNotice((data && data.message) || 'Không thể thêm sản phẩm vào giỏ hàng', true);
                }
            })
            .catch(function() {
                form.dataset.ajaxBypass = '1';
                form.submit();
            });
        }

        var forms = document.querySelectorAll('form[action*="url=gio-hang"]');
        if (!forms.length) return;

        forms.forEach(function(form) {
            if (!form.querySelector('input[name="product_id"]')) return;
            form.addEventListener('submit', function(e) {
                if (form.dataset.ajaxBypass === '1') {
                    return;
                }
                var submitBtn = form.querySelector('button[name="add_to_cart"], input[name="add_to_cart"]');
                if (!submitBtn) return;
                e.preventDefault();
                handleAddToCartForm(form);
            });
        });

        // Capture click trên nút thêm giỏ để đảm bảo không bị submit thường.
        document.addEventListener('click', function(e) {
            var btn = e.target.closest('button[name="add_to_cart"], input[name="add_to_cart"]');
            if (!btn) return;
            var form = btn.closest('form');
            if (!form || !form.matches('form[action*="url=gio-hang"]')) return;
            e.preventDefault();
            handleAddToCartForm(form);
        }, true);
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', hookAjaxAddToCart);
        document.addEventListener('DOMContentLoaded', bindMiniCartActions);
    } else {
        hookAjaxAddToCart();
        bindMiniCartActions();
    }

    if (window.__cartToastSuccess) {
        showCenterCartNotice(window.__cartToastSuccess, false);
    }
})();
</script>

<?php if (isset($_SESSION['user']) && !empty($_SESSION['user']['username'])): ?>
<script>
(function() {
    var isHomePage = <?=isset($_GET['url']) ? 'false' : 'true'?>;
    var expireAtKey = 'home_welcome_expire_at';
    var prevHomeKey = 'home_welcome_prev_is_home';
    var doneKey = 'home_welcome_cycle_done';
    var durationMs = 5000;
    var now = Date.now();
    var expireAt = parseInt(sessionStorage.getItem(expireAtKey) || '0', 10);
    var prevIsHome = sessionStorage.getItem(prevHomeKey) === '1';
    var cycleDone = sessionStorage.getItem(doneKey) === '1';

    // Bắt đầu vòng chào mới khi vừa đi từ trang khác về trang chủ.
    // F5 tại trang chủ (prevIsHome=true) sẽ không khởi tạo lại => không spam.
    if (
        isHomePage &&
        !prevIsHome &&
        (!expireAt || isNaN(expireAt) || expireAt <= now || cycleDone)
    ) {
        expireAt = now + durationMs;
        sessionStorage.setItem(expireAtKey, String(expireAt));
        sessionStorage.setItem(doneKey, '0');
    }

    // Luôn cập nhật trạng thái trang hiện tại để lần tải sau biết là F5 hay chuyển trang.
    sessionStorage.setItem(prevHomeKey, isHomePage ? '1' : '0');

    // Không có countdown hợp lệ thì không hiển thị.
    if (!expireAt || isNaN(expireAt) || expireAt <= now) {
        sessionStorage.removeItem(expireAtKey);
        sessionStorage.setItem(doneKey, '1');
        return;
    }

    function showWelcomePopup(remainingMs) {
        var old = document.getElementById('welcome-user-popup');
        if (old) old.remove();

        var wrap = document.createElement('div');
        wrap.id = 'welcome-user-popup';
        wrap.innerHTML = 'Xin chào <strong><?=htmlspecialchars($_SESSION['user']['username'], ENT_QUOTES)?></strong>, chúc bạn một ngày tốt lành!';
        wrap.style.position = 'fixed';
        wrap.style.top = '86px';
        wrap.style.left = '50%';
        wrap.style.transform = 'translateX(-50%)';
        wrap.style.zIndex = '99998';
        wrap.style.minWidth = '360px';
        wrap.style.maxWidth = '92vw';
        wrap.style.padding = '14px 20px';
        wrap.style.borderRadius = '14px';
        wrap.style.background = 'linear-gradient(135deg, #eefbff 0%, #f3f9ff 55%, #fff5fb 100%)';
        wrap.style.color = '#1f3b57';
        wrap.style.fontSize = '18px';
        wrap.style.lineHeight = '1.4';
        wrap.style.textAlign = 'center';
        wrap.style.border = '1px solid #d9ecff';
        wrap.style.boxShadow = '0 14px 30px rgba(18, 82, 145, 0.18)';
        wrap.style.opacity = '1';
        wrap.style.transition = 'opacity .55s ease';
        wrap.style.pointerEvents = 'none';

        document.body.appendChild(wrap);

        setTimeout(function() {
            wrap.style.opacity = '0';
        }, Math.max(0, remainingMs - 700));

        setTimeout(function() {
            if (wrap && wrap.parentNode) wrap.parentNode.removeChild(wrap);
            sessionStorage.setItem(doneKey, '1');
        }, remainingMs);
    }

    var remaining = expireAt - now;
    remaining = Math.max(0, remaining);

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function() {
            showWelcomePopup(remaining);
        });
    } else {
        showWelcomePopup(remaining);
    }
})();
</script>
<?php endif; ?>

<!-- dialogflow -->
<!-- <script src="https://www.gstatic.com/dialogflow-console/fast/messenger/bootstrap.js?v=1"></script>
<df-messenger
    intent="WELCOME"
    chat-title="Chat"
    agent-id="a111a74a-8334-4098-9636-0f1433d6fc97"
    language-code="vi"
></df-messenger> -->


</body>

</html>
