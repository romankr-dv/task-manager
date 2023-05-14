import React from 'react';
import Helper from "../../App/Helper";
import PanelHeading from "../../App/PanelHeading/PanelHeading";
import Button from "../../App/Common/Button";
import PanelHeadingTask from "../../App/PanelHeading/PanelHeadingTask/PanelHeadingTask";
import OpenIcon from "../../App/Common/OpenIcon";

const TaskPanelHeading = ({title, icon, root, events}) => {
  const renderPanelHeadingTask = (root) => {
    if (root) {
      const backLink = Helper.getTaskPageUrl(root?.parent);
      return <PanelHeadingTask task={root} backLink={backLink}/>;
    }
  }
  return (
    <PanelHeading title={title} icon={icon}>
      {renderPanelHeadingTask(root)}
      <Button onClick={() => events.toggleCalendar()} tooltip="Show calendar"><OpenIcon name="credit-card"/></Button>
      <Button onClick={() => events.reload()} tooltip="Refresh"><OpenIcon name="reload"/></Button>
      <Button onClick={() => events.createNewTask(root?.id)} tooltip="Add task"><OpenIcon name="plus"/></Button>
    </PanelHeading>
  );
}

export default TaskPanelHeading;
