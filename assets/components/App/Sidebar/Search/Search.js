import React, {useState} from 'react';
import "./Search.scss";
import Helper from "../../Helper";
import Config from "../../Config";

const Search = ({onSearch}) => {
  const [value, setValue] = useState("");
  const onChange = (e) => {
    let value = e.target.value;
    if (!value) {
      value = undefined
    }
    setValue(value);
    Helper.addTimeout("search", () => onSearch(value), Config.updateSearchTimeout)
  }
  let className = "form-control search-query";
  if (value) {
    className += " searching";
  }
  return (
    <input type="text" onChange={onChange} className={className} placeholder="Search"/>
  );
}

export default Search;
