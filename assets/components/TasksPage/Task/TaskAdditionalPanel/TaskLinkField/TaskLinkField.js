import React, {useState} from 'react';

const TaskLinkField = ({task, events, children}) => {
  const [isLinkChanging, setLinkChanging] = useState(false);
  const linkClassName = isLinkChanging ? "changing" : "";
  const onLinkChange = event => events.updateTaskLink(task.id, event.target.value, setLinkChanging);
  return (
    <div className="field link">
      <span>Link:</span>
      <input type="text" value={task.link ?? ''} onChange={onLinkChange} className={linkClassName}/>
      <div className="field-children">{children}</div>
    </div>
  );
}

export default TaskLinkField;
