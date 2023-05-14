import React from 'react';
import {Link} from "react-router-dom";
import './PanelHeadingTask.scss';
import OpenIcon from "../../Common/OpenIcon";
import Button from "../../Common/Button";

const PanelHeadingTask = ({task, backLink}) => {
  return (
    <div className="panel-heading-task">
      {task.title ? <span className="panel-heading-task-title" title={task.title}>{task.title}</span> : null}
      {backLink ? <Link to={backLink}><Button tooltip="Go back"><OpenIcon name="share-boxed"/></Button></Link> : null}
    </div>
  );
}

export default PanelHeadingTask;
