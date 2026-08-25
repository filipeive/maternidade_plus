/**
 * Maternidade Plus - Módulo de Alerta Precoce
 * Offline Resilience & IndexedDB Action Queue Helper
 */

(function() {
    'use strict';

    const DB_NAME = 'MaternidadePlusOfflineDB';
    const DB_VERSION = 1;
    const STORE_NAME = 'alert_actions_queue';

    let db = null;

    // Inicialização do IndexedDB
    function initIndexedDB() {
        if (!('indexedDB' in window)) {
            console.warn('[Offline Queue] IndexedDB não suportado neste navegador.');
            return;
        }

        const request = window.indexedDB.open(DB_NAME, DB_VERSION);

        request.onerror = function(event) {
            console.error('[Offline Queue] Erro ao abrir IndexedDB:', event.target.error);
        };

        request.onsuccess = function(event) {
            db = event.target.result;
            console.log('[Offline Queue] IndexedDB inicializado com sucesso.');
            if (navigator.onLine) {
                syncOfflineActions();
            }
        };

        request.onupgradeneeded = function(event) {
            const dbInstance = event.target.result;
            if (!dbInstance.objectStoreNames.contains(STORE_NAME)) {
                dbInstance.createObjectStore(STORE_NAME, { keyPath: 'id', autoIncrement: true });
                console.log('[Offline Queue] ObjectStore criado:', STORE_NAME);
            }
        };
    }

    // Salvar ação no IndexedDB
    function queueOfflineAction(actionData) {
        if (!db) return Promise.reject('IndexedDB não inicializado');

        return new Promise((resolve, reject) => {
            const transaction = db.transaction([STORE_NAME], 'readwrite');
            const store = transaction.objectStore(STORE_NAME);
            const request = store.add({
                ...actionData,
                timestamp: new Date().toISOString()
            });

            request.onsuccess = () => resolve();
            request.onerror = (e) => reject(e.target.error);
        });
    }

    // Sincronizar ações pendentes quando a conexão voltar
    function syncOfflineActions() {
        if (!db || !navigator.onLine) return;

        const transaction = db.transaction([STORE_NAME], 'readwrite');
        const store = transaction.objectStore(STORE_NAME);
        const request = store.getAll();

        request.onsuccess = function() {
            const pendingActions = request.result || [];
            if (pendingActions.length === 0) return;

            console.log(`[Offline Queue] Sincronizando ${pendingActions.length} ações pendentes...`);

            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

            pendingActions.forEach((item) => {
                fetch(item.url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken || ''
                    },
                    body: JSON.stringify({
                        status: item.status,
                        nota: item.nota
                    })
                })
                .then(response => {
                    if (response.ok) {
                        const delTx = db.transaction([STORE_NAME], 'readwrite');
                        delTx.objectStore(STORE_NAME).delete(item.id);
                        console.log(`[Offline Queue] Ação #${item.id} sincronizada e removida da fila.`);
                    }
                })
                .catch(err => {
                    console.warn(`[Offline Queue] Falha ao sincronizar ação #${item.id}:`, err);
                });
            });
        };
    }

    // Atualização dos indicadores visuais de conexão
    function updateOnlineStatus() {
        const offlineBanner = document.getElementById('offline-banner');
        const connectionStatus = document.getElementById('connection-status');
        const connectionText = document.getElementById('connection-text');

        if (navigator.onLine) {
            if (offlineBanner) {
                offlineBanner.classList.add('d-none');
            }
            if (connectionStatus) {
                connectionStatus.classList.remove('offline', 'd-none');
                connectionStatus.classList.add('online');
                if (connectionText) connectionText.textContent = 'Conectado à rede';
                setTimeout(() => connectionStatus.classList.add('d-none'), 3000);
            }
            syncOfflineActions();
        } else {
            if (offlineBanner) {
                offlineBanner.classList.remove('d-none');
            }
            if (connectionStatus) {
                connectionStatus.classList.remove('online', 'd-none');
                connectionStatus.classList.add('offline');
                if (connectionText) connectionText.textContent = 'Sem conexão (Modo Offline)';
            }
        }
    }

    // Interceptar submissão de formulários de resolução de alertas quando offline
    function setupFormInterception() {
        document.addEventListener('submit', function(e) {
            const form = e.target;
            if (form && form.getAttribute('action') && form.getAttribute('action').includes('/alertas/') && form.getAttribute('action').includes('/transitar')) {
                if (!navigator.onLine) {
                    e.preventDefault();
                    const url = form.getAttribute('action');
                    const statusInput = form.querySelector('select[name="status"]');
                    const notaInput = form.querySelector('textarea[name="nota"]');

                    const actionData = {
                        url: url,
                        status: statusInput ? statusInput.value : 'resolvido',
                        nota: notaInput ? notaInput.value : 'Resolução gravada em modo offline.'
                    };

                    queueOfflineAction(actionData).then(() => {
                        alert('Você está offline. A ação foi salva localmente e será sincronizada automaticamente assim que a conexão retornar.');
                        const modalEl = form.closest('.modal');
                        if (modalEl && window.bootstrap) {
                            const modalInstance = window.bootstrap.Modal.getInstance(modalEl);
                            modalInstance?.hide();
                        }
                    }).catch(err => {
                        console.error('Erro ao guardar ação offline:', err);
                    });
                }
            }
        });
    }

    window.addEventListener('online', updateOnlineStatus);
    window.addEventListener('offline', updateOnlineStatus);

    document.addEventListener('DOMContentLoaded', () => {
        initIndexedDB();
        updateOnlineStatus();
        setupFormInterception();
    });

    window.MaternidadeOffline = {
        queueAction: queueOfflineAction,
        syncActions: syncOfflineActions
    };
})();
