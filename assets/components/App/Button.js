import React from 'react';

const Button = ({onClick, className, buttonStyle, buttonSize, onFocus, tooltip, children}) => {
  buttonStyle = buttonStyle ?? 'default';
  const prepareClassName = () => {
    let preparedClassName = ' btn btn-' + buttonStyle + ' ';
    if (buttonSize) {
      preparedClassName += ' btn-' + buttonSize + ' ';
    }
    if (className) {
      preparedClassName += ' ' + className;
    }
    return preparedClassName;
  }
  const wrappedOnFocus = (e) => {
    if (onFocus) {
      onFocus(e);
    }
    e.target.blur();
  }
  return (
    <button onClick={onClick}
            className={prepareClassName()}
            onFocus={wrappedOnFocus}
            data-tooltip-id="basic-tooltip"
            data-tooltip-content={tooltip}>
      {children}
    </button>
  );
}

export default Button;
