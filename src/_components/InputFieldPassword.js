import React from 'react'; 
function InputFieldPassword(props) {
  return <div className="form-group">
    <label>Password</label>
    <input type="password" name="password" value={props.password} onChange={props.handleChange} className={'form-control' + (props.submitted && !props.password ? ' is-invalid' : '')} />
    {props.submitted && !props.password &&
        <div className="invalid-feedback">Password is required</div>
    }
</div>;
}

export default InputFieldPassword;