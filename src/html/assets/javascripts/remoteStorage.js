document.getElementById('connect-button').onclick = function(e) {
  window.open('/assets/html/remoteStorage.html');
  e.preventDefault();
};
window.addEventListener('storage', function(e) {
  console.log(e);
  if(e.key == 'remoteStorageApi') {
    document.getElementById('remoteStorageApi').value=e.newValue;
    localStorage.removeItem('remoteStorageApi');
  }
  if(e.key == 'remoteStorageBaseAddress') {
    document.getElementById('remoteStorageBaseAddress').value=e.newValue;
    localStorage.removeItem('remoteStorageBaseAddress');
  }
  if(e.key == 'remoteStorageToken') {
    document.getElementById('remoteStorageToken').value=e.newValue;
    localStorage.removeItem('remoteStorageToken');
  }
}, true);
