import React from 'react'; 
function InputFieldUsername(props) {
  return <div className="form-group">
  <label>Username</label>
  <input type="text" name="username" value={props.username} onChange={props.handleChange} className={'form-control' + (props.submitted && !props.username ? ' is-invalid' : '')} />
  {props.submitted && !props.username &&
      <div className="invalid-feedback">Username is required</div>
  }
</div>;
}

export default InputFieldUsername;