import config from 'config';
import axios from 'axios';
import { authHeader } from '../_helpers';

export const userService = {
    login,
    logout,
    getAll,
};

function login(username, password) {

    return new Promise((resolve, reject) => {

        const data = {
            username: username,
            password: password
        }
        
        axios.post(`${config.apiUrl}/users/authenticate`, data, {
            crossDomain: true,
            validateStatus: (status) => (status >= 200 && status < 300),
        })
          .then((response) => {

            if (!response.data.success) {
                return reject({ status: 400, text: 'Username or password is incorrect' });
            }
    
            localStorage.setItem('user', JSON.stringify(response.data));
    
            return resolve({
                ok: true, 
                text: () => Promise.resolve(JSON.stringify({
                    id: response.data.id,
                    username: response.data.username,
                    firstName: response.data.firstName,
                    lastName: response.data.lastName,
                    token: response.data.token,
                }))
            });
          })
          .catch((err) => {
            return reject({ status: 400, text: 'Username or password is incorrect' });
          });
	});   
}

function logout() {
    // remove user from local storage to log user out
    localStorage.removeItem('user');
}

function getAll() {

    return new Promise((resolve, reject) => {

        axios.get(`${config.apiUrl}/users/users`, {
            crossDomain: true,
            headers: authHeader(),
            validateStatus: (status) => (status >= 200 && status < 300),
        })
            .then((response) => {
                return resolve(response.data.items);
            })
            .catch((err) => {

                logout();
                location.reload(true);
            });
	});
}
