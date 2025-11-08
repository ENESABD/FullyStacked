const API_BASE = 'http://localhost:8000';

function callEndpoint(method, endpoint, params = [], requiresAuth = false) {
    const formContainer = document.getElementById('form-container');
    const responseContainer = document.getElementById('response-container');
    
    responseContainer.classList.remove('active');
    
    if (params.length === 0) {
        executeRequest(method, endpoint, {}, requiresAuth);
    } else {
        showForm(method, endpoint, params, requiresAuth);
    }
}

function showForm(method, endpoint, params, requiresAuth) {
    const formContainer = document.getElementById('form-container');
    
    let formHTML = `<h3>${method} ${endpoint}</h3>`;
    
    params.forEach(param => {
        const isTextArea = ['notes', 'description'].includes(param);
        const inputType = param === 'password' ? 'password' : 
                         param === 'email' ? 'email' : 
                         ['price', 'recipient_id', 'id', 'recipientId'].includes(param) ? 'number' : 'text';
        
        formHTML += `
            <div class="form-group">
                <label for="${param}">${param}:</label>
                ${isTextArea ? 
                    `<textarea id="${param}" name="${param}"></textarea>` :
                    `<input type="${inputType}" id="${param}" name="${param}" ${param === 'purchased' ? 'min="0" max="1"' : ''}>`
                }
            </div>
        `;
    });
    
    const paramsString = JSON.stringify(params).replace(/"/g, '&quot;');
    formHTML += `<button class="submit-btn" onclick='submitForm("${method}", "${endpoint}", ${JSON.stringify(params)}, ${requiresAuth})'>Submit</button>`;
    
    formContainer.innerHTML = formHTML;
    formContainer.classList.add('active');
}

function submitForm(method, endpoint, params, requiresAuth) {
    const formData = {};
    const urlParams = {};
    
    params.forEach(param => {
        const element = document.getElementById(param);
        if (!element) {
            console.error('Element not found:', param);
            return;
        }
        
        const value = element.value;
        
        if (param === 'id' || param === 'recipientId') {
            urlParams[param] = value;
        } else {
            if (value !== '') {
                if (param === 'purchased') {
                    formData[param] = parseInt(value);
                } else if (param === 'price' || param === 'recipient_id') {
                    formData[param] = param === 'price' ? parseFloat(value) : parseInt(value);
                } else {
                    formData[param] = value;
                }
            }
        }
    });
    
    console.log('Form Data:', formData);
    console.log('URL Params:', urlParams);
    
    let finalEndpoint = endpoint;
    Object.keys(urlParams).forEach(key => {
        finalEndpoint = finalEndpoint.replace(`:${key}`, urlParams[key]);
    });
    
    executeRequest(method, finalEndpoint, formData, requiresAuth);
}

async function executeRequest(method, endpoint, data, requiresAuth) {
    const responseContainer = document.getElementById('response-container');
    
    const options = {
        method: method,
        headers: {
            'Content-Type': 'application/json'
        }
    };
    
    if (requiresAuth) {
        const token = document.getElementById('token').value;
        if (!token) {
            showResponse({
                success: false,
                error: { message: 'Bearer token is required for this endpoint. Please login first.' }
            }, 401);
            return;
        }
        options.headers['Authorization'] = `Bearer ${token}`;
    }
    
    if (method !== 'GET' && method !== 'DELETE' && Object.keys(data).length > 0) {
        options.body = JSON.stringify(data);
    }
    
    try {
        const response = await fetch(API_BASE + endpoint, options);
        const responseData = await response.json();
        
        if (responseData.success && responseData.data && responseData.data.token) {
            document.getElementById('token').value = responseData.data.token;
        }
        
        showResponse(responseData, response.status);
    } catch (error) {
        showResponse({
            success: false,
            error: { message: error.message }
        }, 0);
    }
}

function showResponse(data, status) {
    const responseContainer = document.getElementById('response-container');
    
    const isSuccess = status >= 200 && status < 300;
    const statusClass = isSuccess ? 'status-success' : 'status-error';
    const responseClass = isSuccess ? 'response-success' : 'response-error';
    
    const html = `
        <h3>Response</h3>
        <span class="status-badge ${statusClass}">HTTP ${status}</span>
        <div class="${responseClass}">
            <strong>${data.success ? 'Success' : 'Error'}</strong>
        </div>
        <pre>${JSON.stringify(data, null, 2)}</pre>
    `;
    
    responseContainer.innerHTML = html;
    responseContainer.classList.add('active');
    responseContainer.scrollIntoView({ behavior: 'smooth' });
}

function clearToken() {
    document.getElementById('token').value = '';
}
