import React from 'react';
import './TaskParentButton.scss';
import Helper from "../../../App/Helper";
import Icon from "../../../App/Common/Icon";
import {Link} from "react-router-dom";

const TaskParentButton = ({parent}) => {
  return (
    <Link to={Helper.getTaskPageUrl(parent.id)} className='task-parent'>
      <Icon name="tasks"/>
      {parent.title}
    </Link>
  )
}

export default TaskParentButton;
