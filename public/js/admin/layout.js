/* Admin Layout JS — sidebar toggle etj.
   resources/js/admin/layout.js
*/

function toggleSidebar() {
            document.querySelector('.sidebar').classList.toggle('open');
            document.getElementById('sidebarOverlay').classList.toggle('open');
            document.body.style.overflow = document.querySelector('.sidebar').classList.contains('open') ? 'hidden' : '';
        }
        function closeSidebar() {
            document.querySelector('.sidebar').classList.remove('open');
            document.getElementById('sidebarOverlay').classList.remove('open');
            document.body.style.overflow = '';
        }