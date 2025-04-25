</div> <!-- Penutup container-fluid -->
        </div> <!-- Penutup main-content -->
    </div> <!-- Penutup flex container -->

    <script src="../assets/js/bootstrap.bundle.min.js"></script>
    <script>
        // Fungsi toggle sidebar untuk mobile
        function toggleSidebar() {
            document.getElementById('sidebar').classList.toggle('show');
        }
        
        // Aktifkan tooltip
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    </script>
</body>
</html>