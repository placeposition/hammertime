import React, { useEffect } from 'react';
import { Link } from 'react-router-dom';
import { useDispatch, useSelector } from 'react-redux';

import { userActions } from '../_actions';

function HomePage() {
    const users = useSelector(state => state.users);
    const user = useSelector(state => state.authentication.user);
    const dispatch = useDispatch();

    useEffect(() => {
        dispatch(userActions.getAll());
    }, []);

    return (
        <div className="col-lg-8 offset-lg-2">
            <h1>Welcome {user.firstName}</h1>
            <h3>All registered users:</h3>
            {users.loading && <em>Loading users...</em>}
            {users.error && <span className="text-danger">ERROR: {users.error}</span>}
            {users.items &&
                <table className="table table-condensed">
                    <tbody>
                    {users.items.map((user, index) =>
                        <tr key={user.id}>
                            <td>{user.firstName + ' ' + user.lastName}</td>
                            <td>{user.role}</td>
                            <td>{user.bankdetails.sortcode}</td>
                            <td>{user.bankdetails.account}</td>
                        </tr>
                    )}
                    </tbody>
                </table>
            }
            <p>
                <Link to="/login">Logout</Link>
            </p>
        </div>
    );
}

export { HomePage };