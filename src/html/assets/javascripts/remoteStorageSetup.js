document.getElementById('connect-button').onclick = function(e) {
  localStorage.setItem('_remoteStorage/userAddress', document.getElementById('connect-address').value);
  window.open('/assets/html/remoteStorage.html');
  e.preventDefault();
};
window.addEventListener('storage', function(e) {
  console.log(e);
  if(e.key == '_remoteStorage/api') {
    document.getElementById('remoteStorageApi').value=e.newValue;
    localStorage.removeItem('_remoteStorage/api');
  }
  if(e.key == '_remoteStorage/baseAddress') {
    document.getElementById('remoteStorageBaseAddress').value=e.newValue;
    localStorage.removeItem('_remoteStorage/baseAddress');
  }
  if(e.key == '_remoteStorage/token') {
    document.getElementById('remoteStorageToken').value=e.newValue;
    localStorage.removeItem('_remoteStorage/token');
  }
}, true);
